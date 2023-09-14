<?php

namespace App\Type;

class BlobIndex
{

    /**
     * The revision of the index
     *
     * @var string|null
     */
    public string $rev = "1.1";

    /**
     * @var BlobIndexMedia[]
     */
    public $media = [];

    /**
     * @var int
     */
    public int $lastId = 1;

    /**
     * @var string
     */
    public $scope = "";

    /**
     * @var string
     */
    public $baseUrl = "";
}
