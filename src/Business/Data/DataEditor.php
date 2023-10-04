<?php

namespace App\Business\Data;

use Lack\Frontmatter\Repo\FrontmatterRepo;
use Lack\Frontmatter\Repo\FrontmatterRepoPid;
use Lack\OpenAi\Attributes\AiFunction;
use Lack\OpenAi\Attributes\AiParam;
use Lack\OpenAi\Helper\JobTemplate;
use Lack\OpenAi\LackOpenAiClient;
use Lack\OpenAi\LackOpenAiResponse;
use Leuffen\Brix\Functions\SingleFileAccessFunctions;
use Leuffen\Brix\Plugins\Seo\SeoAnalyzer;
use Phore\Cli\CLIntputHandler;
use Phore\FileSystem\PhoreFile;

class DataEditor
{

    public function __construct(
        public string $context,
        public LackOpenAiClient $client
    ){

    }


    public function adjust (PhoreFile $file) {
        $tpl = new JobTemplate(__DIR__ . "/job-data-adjust.txt");

        $tpl->setData([
            "context" => $this->context,
            "fileType" => $file->getExtension(),
        ]);
        $this->client->reset($tpl->getSystemContent(), 0.1);
        $this->client->getCache()->clear();
        $this->client->textComplete([
            $file->get_contents(),
            $tpl->getUserContent()
        ], streamer: function (LackOpenAiResponse $response) use ($file) {
            $data = $response->getTextCleaned();
            $file->set_contents($data);
        });

    }




}
