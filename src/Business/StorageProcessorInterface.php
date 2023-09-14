<?php

namespace App\Business;

use App\Type\BlobIndex;
use Phore\ObjectStore\ObjectStore;

interface StorageProcessorInterface
{

    public function isSuitable(string $extension) : bool;

    public function process(string $filename, string $fileExtension, string $dataFile, BlobIndex $index, ObjectStore $objectStore, string $scope);

}
