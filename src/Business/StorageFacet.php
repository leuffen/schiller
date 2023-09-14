<?php

namespace App\Business;

use App\Transformer\ImageTransformer;
use App\Transformer\SvgTransformer;
use App\Transformer\Transformer;
use App\Type\BlobIndex;
use App\Type\BlobIndexMedia;
use Phore\ObjectStore\ObjectStore;

class StorageFacet
{

    /**
     * @var BlobIndex
     */
    public $index;
    public $indexObj;

    /**
     * @var StorageProcessorInterface[]
     */
    private array $processors = [];

    public function __construct(
        public ObjectStore $publicStore,
        public ObjectStore $privateStore,
        public string      $scope) {

        $this->indexObj = $this->privateStore->object($this->scope . "/.index.media-v2.1.json");
        if ( ! $this->indexObj->exists()) {
            $this->index = new BlobIndex();
        } else {
            $this->index = phore_hydrate($this->indexObj->getJson(), BlobIndex::class);
        }

    }

    protected function sanitizeName($name) {
        return preg_replace("/[^a-z0-9_\-\.]/im", "", $name);
    }

    public function saveIndex() {
        out("Save Index");
        $this->indexObj->putJson((array)$this->index);
    }

    public function getIndex() {
        return $this->index;
    }



    public function addProcessor(StorageProcessorInterface $processor) : void {
        $this->processors[] = $processor;
    }


    /**
     * Store the file in the storage
     *
     * @param string $origFilename
     * @param string $tempFile
     * @return void
     */
    public function store(string $origFilename, string $tempFile) : void {
        $filename = $this->sanitizeName($origFilename);
        $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $filename = pathinfo($filename, PATHINFO_FILENAME);
        $processorFound = false;
        foreach ($this->processors as $processor) {
            if ( ! $processor->isSuitable($fileExtension))
                continue;
            $processor->process($filename, $fileExtension, $tempFile, $this->index, $this->publicStore, $this->scope);
            $processorFound = true;
        }

        if ( ! $processorFound) {
            throw new \InvalidArgumentException("No processor found for file extension: $fileExtension");
        }

        $this->saveIndex();

    }

}
