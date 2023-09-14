<?php

namespace App\Ctrl;

use App\Business\StorageFacet;
use App\Config\MediaStoreConf;
use Brace\Router\Attributes\BraceRoute;

class GalleryCtrl
{

    #[BraceRoute("GET@/{subscription_id}/{scope_id}/index.json")]
    public function index(StorageFacet $storageFacet, MediaStoreConf $mediaStoreConf) {
        $index = $storageFacet->getIndex();
        $index->baseUrl = CONF_URL_PREFIX . "/" . $mediaStoreConf->scope . "/";
        $index->scope = $mediaStoreConf->scope;
        return (array)$index;
    }
}
