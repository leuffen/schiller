<?php
namespace App;

use App\Business\processors\DownloadStorageProcessor;
use App\Business\processors\ImageStorageProcessor;
use App\Business\processors\PdfStorageProcessor;
use App\Business\processors\SvgStorageProcessor;
use App\Business\StorageFacet;
use App\Business\Template;
use App\Config\MediaStoreConf;
use App\Config\MediaStoreSubscriptionInfo;
use App\Type\T_Config;
use Brace\Command\CommandModule;
use Brace\Core\AppLoader;
use Brace\Core\BraceApp;
use Brace\Dbg\BraceDbg;
use Brace\Mod\Request\Zend\BraceRequestLaminasModule;
use Brace\Router\RouterModule;
use Brace\Router\Type\RouteParams;
use Lack\Frontmatter\Repo\FrontmatterRepo;
use Lack\OpenAi\LackOpenAiClient;
use Lack\OpenAi\Logger\NullLogger;
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


    $app->define("config", new DiService(function () {
        $config = phore_hydrate_file(CONF_PATH . "/.schiller.yml", T_Config::class);
        /** @var T_Config $config */
        $config->__setConfigFileLocation(CONF_PATH);
        return $config;
    }));

    $app->define("frontmatterRepo", new DiService(function (T_Config $config) {
        return new FrontmatterRepo(CONF_PATH . "/" . $config->doc_root);
    }));

    $app->define("templateRepo", new DiService(function (T_Config $config) {
        return new FrontmatterRepo(CONF_PATH . "/" . $config->template_dir);
    }));



    $app->define("openai", new DiService(function (T_Config $config) {
        $keystore = phore_file(CONF_KEYSTORE_FILE)->assertFile()->get_yaml();
        return new LackOpenAiClient($keystore["open_ai"], new NullLogger());
    }));

    $app->define("context", new DiService(function (T_Config $config) {
        $contextCombined = "";
        $contextCombined .= phore_file(CONF_PATH . "/" . $config->context_file)->assertFile()->get_contents();

        return $contextCombined;
    }));


    // Define the app so it is also available in dependency-injection
    $app->define("app", new DiValue($app));


    return $app;
});
