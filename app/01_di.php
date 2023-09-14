<?php
namespace App;

use App\Business\processors\DownloadStorageProcessor;
use App\Business\processors\ImageStorageProcessor;
use App\Business\processors\PdfStorageProcessor;
use App\Business\processors\SvgStorageProcessor;
use App\Business\StorageFacet;
use App\Config\MediaStoreConf;
use App\Config\MediaStoreSubscriptionInfo;
use Brace\Command\CommandModule;
use Brace\Core\AppLoader;
use Brace\Core\BraceApp;
use Brace\Dbg\BraceDbg;
use Brace\Mod\Request\Zend\BraceRequestLaminasModule;
use Brace\Router\RouterModule;
use Brace\Router\Type\RouteParams;
use Lack\Subscription\Brace\SubscriptionClientModule;
use Lack\Subscription\Type\T_Subscription;
use Phore\Di\Container\Producer\DiService;
use Phore\Di\Container\Producer\DiValue;
use Phore\ObjectStore\Driver\GoogleObjectStoreDriver;
use Phore\ObjectStore\ObjectStore;


BraceDbg::SetupEnvironment(true, ["192.168.178.20", "localhost", "localhost:5000"]);


AppLoader::extend(function () {
    $app = new BraceApp();

    // Use Laminas (ZendFramework) Request Handler
    $app->addModule(new BraceRequestLaminasModule());

    // Use the Uri-Based Routing
    $app->addModule(new RouterModule());
    $app->addModule(new CommandModule());




    // Define the app so it is also available in dependency-injection
    $app->define("app", new DiValue($app));


    return $app;
});
