<?php

namespace App\Transformer;

use App\Type\BlobIndexMedia;
use App\Type\BlobIndexMediaVariant;

class Helper
{


    /**
     * Return the Aspect Ratio of the given width and height
     *
     * @param int $width
     * @param int $height
     * @return string
     */
    static public function getAspectRatio(int $width, int $height) : string {
        $gcdFn = function ($a, $b) use (&$gcdFn) {
            if ($b == 0) {
                return $a;
            } else {
                return $gcdFn($b, $a % $b);
            }
        };


        $float2rat = function($n, $tolerance = 1.e-4) {
            $h1 = 1; $h2 = 0;
            $k1 = 0; $k2 = 1;
            $b = 1 / $n;
            do {
                $b = 1 / $b;
                $a = floor($b);
                $aux = $h1;
                $h1 = $a*$h1+$h2;
                $h2 = $aux;
                $aux = $k1;
                $k1 = $a*$k1+$k2;
                $k2 = $aux;
                $b = $b-$a;
            } while (abs($n-$h1/$k1) > $n*$tolerance);

            return "$h1/$k1";
        };

        return $float2rat($width / $height);

    }

}
