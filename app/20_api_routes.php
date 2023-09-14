<?php
namespace App;


use App\Ctrl\GalleryCtrl;
use App\Ctrl\InfoCtrl;
use App\Ctrl\UploadCtrl;
use Brace\Auth\Basic\RequireValidAuthTokenMiddleware;
use Brace\Core\AppLoader;
use Brace\Core\BraceApp;


AppLoader::extend(function (BraceApp $app) {

    $mount = CONF_API_MOUNT;

    // Controller classes
    $app->router->registerClass($mount, UploadCtrl::class, [RequireValidAuthTokenMiddleware::class]);
    $app->router->registerClass($mount, GalleryCtrl::class, [RequireValidAuthTokenMiddleware::class]);
    $app->router->registerClass($mount, InfoCtrl::class, [RequireValidAuthTokenMiddleware::class]);


    // Other stuff
    //$app->router->on("POST|GET@$mount/repo", RepoCtrl::class, [RequireValidAuthTokenMiddleware::class]);

    // Return the Api Version
    $app->router->on("GET@$mount", function() {
        return ["system" => "mediastore working", "status" => "ok"];
    });

    // Redirect to static Middleware (Frontend)
    $app->router->on("GET@/", function () use ($app) {
        return $app->redirect("/static");
    });

    if (DEV_MODE === true) {
        $app->router->writeJSStub(__DIR__ . "/../app.fe/_routes.js");
    }

});
