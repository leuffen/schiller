<?php

namespace App\Type;

class T_Config
{

    /**
     * @var string
     */
    public string $doc_root = "./docs";


    public string $context_file = ".schiller-context.txt";

    /**
     * @var string
     */
    public string $template_dir = "./templates";

    /**
     * if absolute path will search absolute. Otherwise relative to template_dir
     *
     * @var string|null
     */
    public ?string $sections_def_file = null;

    /**
     * @var string|null
     */
    private ?string $__configFileLocation = null;


    public function __setConfigFileLocation($path) {
        $this->__configFileLocation = $path;
    }


    public function getSectionDefFile() : string|null {
        if ($this->sections_def_file === null)
            return null;
        if (substr($this->sections_def_file, 0, 1) == "/")
            return $this->__configFileLocation ."/". $this->sections_def_file;


        return $this->__configFileLocation . "/" . $this->template_dir . "/" . $this->sections_def_file;
    }

}
