<?php

namespace App\Config;

use Phore\FileSystem\PhoreDirectory;
use Phore\FileSystem\PhoreUri;

class MediaStoreConf
{


    public function __construct(
        public string $scope,
        public string $subscriptionId,
        public string $access,
        public MediaStoreSubscriptionInfo $mediaStoreSubscriptionInfo
    ) {

    }


}
