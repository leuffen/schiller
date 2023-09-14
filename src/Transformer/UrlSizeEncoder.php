<?php

namespace App\Transformer;


/**
 * Encodes the url in:
 *
 * v/{id}/[format]_[width]_[width]/{filename}.[extions1]_[extension2]
 */
class UrlSizeEncoder
{

    const RATIO_SHORTCUTS = [
        "1-1" => "a",
        "4-3" => "b",
        "3-2" => "c",
        "16-9" => "d",
        "21-9" => "e",
        "3-4" => "B",
        "2-3" => "C",
        "9-16" => "D",
        "9-21" => "E"
    ];

    const WIDTH_SHORTCUTS = [
        "260" => "a",
        "414" => "b",
        "896" => "c",
        "1280" => "d",
        "1440" => "e",
        "1920" => "f",
        "2560" => "g",
    ];


    public $widths = [];
    public $ratio = null;


    public $extensions = [];

    public function __construct(
        public string $id, public string $filename) {

    }

    // Return 0.5 if the aspect ratio is 1:2
    // Return 1.0 if the aspect ratio is 1:1
    // Return 16 : 9 if the aspect ratio is 16:9 etc.
    public function setAspectRatio(int $width, int $height) {
        $this->ratio = Helper::getAspectRatio($width, $height);

    }

    public function setWidths(array $widths) {
        $this->widths = $widths;
        return $this;
    }

    public function addWidth(int $width) {
        $this->widths[] = $width;
    }

    public function setExtensions(array $extensions) {
        $this->extensions = $extensions;
        return $this;
    }



    public function toString() {
        $widths = implode("-", $this->widths);
        $extensions = implode("_", $this->extensions);
        $aspect = str_replace("/", "-", $this->ratio);

        $aspect = preg_replace_callback("/^([0-9\-]+)$/", fn(array $w) => self::RATIO_SHORTCUTS[$w[1]] ?? $w[1], $aspect);
        $widths = preg_replace_callback("/([0-9]+)-?/", fn(array $w) => self::WIDTH_SHORTCUTS[$w[1]] ?? $w[1], $widths);

        return "v2/" . $this->id . "/" . $aspect . "_" . $widths . "/" . $this->filename . "." . $extensions;
    }

}
