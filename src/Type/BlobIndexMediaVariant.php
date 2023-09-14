<?php

namespace App\Type;

class BlobIndexMediaVariant
{

    /**
     * @var string
     */
    public $variantId;

    /**
     * @var int
     */
    public $height;

    /**
     * @var int
     */
    public $width;

    /**
     * @var string[]
     */
    public $extensions = [];

    /**
     * @var string
     */
    public $url = "";
}
