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

class SvgStorageProcessor implements StorageProcessorInterface
{

    public function isSuitable(string $extension): bool
    {
        // Do not use this processor for SVGs! See SvgStorateProcessor
        return in_array(strtolower($extension), ["svg"]);
    }




    public function process(string $filename, string $fileExtension, string $dataFile, BlobIndex $index, ObjectStore $objectStore, string $scope)
    {
        $obj = new BlobIndexMedia();
        $obj->size = filesize($dataFile);
        $obj->id = $index->lastId++;
        $obj->sha = sha1_file($dataFile);
        $obj->uploadDate = date("Y-m-d H:i:s");
        $obj->type = "svg";

        $obj->name = $filename;
        $obj->extension = $fileExtension;

        $xmlget = simplexml_load_string(file_get_contents($dataFile));
        $xmlattributes = $xmlget->attributes();

        if ($xmlattributes->viewBox != "") {
            [$dummy, $dummy, $width, $height] = explode(" ", $xmlattributes->viewBox);
        } else {
            $width = (int) $xmlattributes->width;
            $height = (int) $xmlattributes->height;
        }


        // The original Image
        $namingEncoder = new UrlSizeEncoder($obj->id, $obj->name);
        $namingEncoder->setAspectRatio($width, $height);
        $namingEncoder->setWidths([$width]);
        $namingEncoder->setExtensions([$fileExtension]);

        $obj->previewUrl = $namingEncoder->toString();
        $obj->origUrl = $namingEncoder->toString();
        $obj->info1 = $width . "x". $height;

        $objectStore->object($scope . "/" . $obj->origUrl)
            ->withMeta(["Content-Type"=> "image/svg+xml"])->put(phore_file($dataFile)->get_contents());

        array_unshift($index->media, $obj);
    }
}
