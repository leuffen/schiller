<?php

namespace App\Type;

class BlobIndexMedia
{



    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $sha;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $extension;

    /**
     * @var string[]
     */
    public $tags = [];


    /**
     * @var string
     */
    public $type = "image";

    /**
     * @var int
     */
    public $size;

    /**
     * @var string
     */
    public $uploadDate;


    /**
     * @var string|null
     */
    public $info1 = "";

    /**
     * @var BlobIndexMediaVariant[]
     */
    public $variant = [];

    /**
     * @var string|null
     */
    public string|null $userDescription = "";

    /**
     * @var string
     */
    public $license = "";

    /**
     * @var string
     */
    public $origUrl = "";

    /**
     * @var string
     */
    public $previewUrl = "";
}
