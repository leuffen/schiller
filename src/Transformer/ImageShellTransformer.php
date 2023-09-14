<?php

namespace App\Transformer;

class ImageShellTransformer
{

    public function __construct(private string $inputFile)
    {
    }

    private $dimensions = null;
    private $lastInputFile = null;

    public function getImageMimeType() : string {
        $ret = phore_exec("identify -format '%m' ':file'", ["file" => $this->inputFile]);
        return $ret;
    }

    public function getImageDimensions() : array {
        if ($this->dimensions !== null)
            return $this->dimensions;
        $ret = phore_exec("identify -format '%w %h' ':file'", ["file" => $this->inputFile]);
        $exp = explode(" ", $ret);
        $this->dimensions = [
            "width" => (int)$exp[0],
            "height" => (int)$exp[1]
        ];
        return $this->dimensions;
    }


    public $filenames = [];

    public function convert(string $format, int $width, int $quality) : string {
        if ( ! $this->lastInputFile)
            $this->lastInputFile = $this->inputFile;

        $inputDir = dirname($this->lastInputFile);
        $inputName = basename($this->lastInputFile);

        $outName = $inputDir . "/" .  $inputName . "-" . $width . "."  . $format;
        phore_exec("convert ':input' -quality {quality} -resize '{width}x' ':output'", [
            "input" => $this->lastInputFile,
            "width" => $width,
            "quality" => $quality,
            "output" => $outName
        ]);

        $this->filenames[] = $outName;
        $this->lastInputFile = $outName;

        return $outName;

    }

    public function __destruct()
    {
        foreach ($this->filenames as $file) {
            unlink($file);
        }
    }

}
