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

class ImageStorageProcessor implements StorageProcessorInterface
{

    public function isSuitable(string $extension): bool
    {
        // Do not use this processor for SVGs! See SvgStorateProcessor
        return in_array(strtolower($extension), ["jpg", "jpeg", "png", "gif", "webp", "avif", "tiff"]);
    }

    /**
     * Create these widths; Order important: Start with biggest first!
     *
     */
    const WIDTHS = [
        "2560", "1920", "1440", "1280", "896", "414","260",
    ];


    const QUALITY = 85;

    const QUALITY_PREVIEW = 20;
    const WIDTH_PREVIEW = 180;

    const FORMATS =  [
        "webp" => "image/webp",
    ];


    public function process(string $filename, string $fileExtension, string $dataFile, BlobIndex $index, ObjectStore $objectStore, string $scope)
    {
        $obj = new BlobIndexMedia();
        $obj->size = filesize($dataFile);
        $obj->id = $index->lastId++;
        $obj->sha = sha1_file($dataFile);
        $obj->uploadDate = date("Y-m-d H:i:s");
        $obj->type = "image";

        $obj->name = $filename;
        $obj->extension = $fileExtension;



        $transformer = new ImageShellTransformer($dataFile);
        $dimensions = $transformer->getImageDimensions();

        // The original Image
        $namingEncoder = new UrlSizeEncoder($obj->id, $obj->name);
        $namingEncoder->setAspectRatio($dimensions["width"], $dimensions["height"]);

        $namingEncoder->setExtensions([$fileExtension]);

        $previewName = clone $namingEncoder;
        $previewName->setExtensions(array_keys(self::FORMATS));
        $variantIndex = 0;
        foreach (self::WIDTHS as $width) {
            if ($dimensions["width"] < $width) {
                continue;
            }
            $previewName->addWidth($width);


            foreach (array_keys(self::FORMATS) as $format) {
                $curNamingEncoder = clone $namingEncoder;
                $curNamingEncoder->setWidths([$width]);
                $curNamingEncoder->setExtensions([$format]);


                $variant = new BlobIndexMediaVariant();
                $variant->variantId = $variantIndex++;
                $variant->width = $width;
                $variant->height = (int)($dimensions["height"] / $dimensions["width"] * $width);
                $variant->url = $curNamingEncoder->toString();

                $tmpName = $transformer->convert($format, $width, self::QUALITY);
                $objectStore->object($scope . "/" . $curNamingEncoder->toString())
                    ->withMeta(["Content-Type"=> self::FORMATS[$format]])->put(phore_file($tmpName)->get_contents());

                $variant->extensions = [$format];
                $obj->variant[] = $variant;
            }

        }

        $obj->info1 = Helper::getAspectRatio($dimensions["width"], $dimensions["height"]);

        $namingEncoder->setWidths([$dimensions["width"]]);
        $obj->origUrl = $namingEncoder->toString();
        $obj->previewUrl = $previewName->toString();
        $previewFile = $transformer->convert("webp", self::WIDTH_PREVIEW, self::QUALITY_PREVIEW);
        $objectStore->object($scope . "/" . $obj->previewUrl)
            ->withMeta(["Content-Type"=> "image/webp"])->put(phore_file($previewFile)->get_contents());

        $objectStore->object($scope . "/" . $obj->origUrl)
            ->withMeta(["Content-Type"=> $transformer->getImageMimeType()])->put(phore_file($dataFile)->get_contents());

        array_unshift($index->media, $obj);
    }
}
