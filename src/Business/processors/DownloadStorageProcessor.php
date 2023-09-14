<?php

namespace App\Business\processors;

use App\Business\StorageProcessorInterface;
use App\Transformer\Helper;
use App\Transformer\ImageShellTransformer;
use App\Transformer\ImageTransformer;
use App\Transformer\SvgTransformer;
use App\Transformer\Transformer;
use App\Transformer\UrlSizeEncoder;
use App\Type\BlobIndex;
use App\Type\BlobIndexMedia;
use App\Type\BlobIndexMediaVariant;
use Phore\ObjectStore\ObjectStore;

class DownloadStorageProcessor implements StorageProcessorInterface
{

    const FORMATS = [
        "zip" => "application/zip",
        "mp4" => "video/mp4",
        "avi" => "video/avi",
        "mov" => "video/quicktime",
        "txt" => "text/plain",
        "json" => "application/json",
    ];

    public function isSuitable(string $extension): bool
    {
        // Do not use this processor for SVGs! See SvgStorateProcessor
        return in_array($extension, array_keys(self::FORMATS));
    }




    public function process(string $filename, string $fileExtension, string $dataFile, BlobIndex $index, ObjectStore $objectStore, string $scope)
    {
        $obj = new BlobIndexMedia();
        $obj->size = filesize($dataFile);
        $obj->id = $index->lastId++;
        $obj->sha = sha1_file($dataFile);
        $obj->uploadDate = date("Y-m-d H:i:s");
        $obj->type = "download";

        $obj->name = $filename;
        $obj->extension = $fileExtension;


        $obj->origUrl = "d/" . $obj->id . "/" . $obj->name. "." . $obj->extension;
        $obj->previewUrl = "";

        $obj->info1 = number_format($obj->size / 1024 / 1024, 2) . " MB";

        $objectStore->object($scope . "/" . $obj->origUrl)
            ->withMeta(["Content-Type"=> self::FORMATS[$fileExtension]])->put(phore_file($dataFile)->get_contents());

        array_unshift($index->media, $obj);
    }
}
