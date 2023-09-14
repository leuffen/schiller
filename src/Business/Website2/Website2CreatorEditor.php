<?php

namespace App\Business\Website2;

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

class Website2CreatorEditor
{

    public function __construct(
        public string $context,
        public FrontmatterRepo $targetRepo,
        public LackOpenAiClient $client
    ){

    }



    private $modifiedPages = [];

    public function adjust (FrontmatterRepoPid $pid) {
        $tpl = new JobTemplate(__DIR__ . "/job-adjust.txt");
        $page = $pid->get();

        if (trim ($page->header["_schiller_instructions"]) === "")
            $page->header["_schiller_instructions"] = null;

        $tpl->setData([
            "context" => $this->context,
            "title" => $page->header["title"] ?? "undefined",
            "links" => $this->targetRepo->getPageLinksAsMardownLinks($pid->getLang()),

            "ai_instructions" => $page->header["_schiller_instructions"] ?? "Schreibe den Text auf den Context um!"
        ]);
        $this->client->reset($tpl->getSystemContent(), 0.4);
        $this->client->getCache()->clear();
        $this->client->textComplete([
            $page->body,
            $tpl->getUserContent()
        ], streamer: function (LackOpenAiResponse $response) use ($page) {
            $page->body = $response->getTextCleaned();
            $this->targetRepo->storePage($page);
        });

    }

}
