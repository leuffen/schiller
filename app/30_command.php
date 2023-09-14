<?php
namespace App;

use Brace\Core\AppLoader;
use Brace\Core\BraceApp;
use Phore\ObjectStore\ObjectStore;

AppLoader::extend(function (BraceApp $app) {
    $app->command->addCommand("testBucket", function (ObjectStore $objectStore) {
        $obj = $objectStore->object("wurst1.html");
        $obj->put("<h1>hellosdfsdf 2 world</h1>");
        $obj->setMeta(["Cache-Control" => "public, max-age=36000", "expires" => gmdate('D, d M Y H:i:s \G\M\T', time() + (100 * 60 * 60))]);
        out($obj->get());
    });
});


