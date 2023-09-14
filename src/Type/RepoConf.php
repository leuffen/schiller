<?php

namespace App\Type;

use App\Helper\Project;
use Phore\FileSystem\PhoreDirectory;
use Phore\FileSystem\PhoreFile;
use Phore\FileSystem\PhoreUri;

class RepoConf
{

    const PROJECTS_PATH = "/projects";
    const ELEMENTS_PATH = "/elements";
    const PROJECTS_PROTOTYPE = "/project_prototype";

    public function __construct(string $repo_dir) {
        $this->repo_dir = $repo_dir;
    }

    /**
     * @var string|null
     */
    public string $repo_dir;



    /**
     * The directory the git-repo is cloned into
     *
     * @return PhoreDirectory
     */
    public function getRepoDir() : PhoreDirectory
    {
        return phore_dir($this->repo_dir);
    }


    public function getProject($projectName) : Project {
        return new Project($this->getProjectPath($projectName), $this->getElementsPath());
    }


    public function getPresetDir(string $version=null) : PhoreDirectory
    {
        $dir = $this->getRepoDir()->withSubPath("presets");
        if ($version !== null)
            $dir = $dir->withSubPath($version)->assertDirectory();
        return phore_dir($dir);
    }

    public function getProjectPath($project = null) : PhoreDirectory
    {
        return $this->getRepoDir()
            ->withRelativePath(self::PROJECTS_PATH)
            ->withRelativePath((string)$project)
            ->asDirectory();
    }

    public function getElementsPath() : PhoreDirectory
    {
        return $this->getRepoDir()->withSubPath(self::ELEMENTS_PATH)->assertDirectory();
    }



}
