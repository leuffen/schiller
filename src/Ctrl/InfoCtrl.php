<?php

namespace App\Ctrl;

use App\Config\MediaStoreConf;
use Brace\Router\Attributes\BraceRoute;
use Laminas\Diactoros\ServerRequest;

class InfoCtrl
{

    #[BraceRoute("GET@/{subscription_id}/info")]
    public function info(ServerRequest $request, MediaStoreConf $mediaStoreConf) {
        return [
            "scopes" => $mediaStoreConf->mediaStoreSubscriptionInfo->getScopeNames()
        ];
    }

}
