<?php

namespace App\Transformer;

class PdfShellTransformer
{


    public static function  Convert(string $inputFile) : string {
        phore_exec("pdftocairo -png -singlefile ':input' ':output'", ["input" => $inputFile, "output" => $inputFile]);
        return $inputFile . ".png";
    }

}
