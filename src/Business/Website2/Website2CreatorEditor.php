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

        if (trim ($page->header["_schiller_instructions"] ?? "") === "")
            $page->header["_schiller_instructions"] = null;

        $tpl->setData([
            "context" => $this->context,
            "title" => $page->header["title"] ?? "undefined",
            "links" => $this->targetRepo->getPageLinksAsMardownLinks($pid->getLang(), true),

            "ai_instructions" => $page->header["_schiller_instructions"] ?? "Schreibe den Text auf den Context um!"
        ]);
        $this->client->reset($tpl->getSystemContent(), 0.05, "gpt-4o");
        $this->client->getCache()->clear();
        $this->client->textComplete([
            $page->body,
            $tpl->getUserContent()
        ], streamer: function (LackOpenAiResponse $response) use ($page) {
            $page->body = $response->getTextCleaned();
            $this->targetRepo->storePage($page);
        });

    }


    public function generateMeta(FrontmatterRepoPid $pid) {
        $tpl = new JobTemplate(__DIR__ . "/job-generateMeta.txt");
        $page = $pid->get();

        $tpl->setData([
            "context" => $this->context,
            "title" => $page->header["title"] ?? "undefined",
            "content" => $page->body,
        ]);
        $this->client->reset($tpl->getSystemContent(), 0.05, "gpt-4o");
        $this->client->getCache()->clear();
        $ret = $this->client->textComplete([
            $tpl->getUserContent()
        ]);
        $page->header["description"] = $ret->getTextCleaned();
        $this->targetRepo->storePage($page);
    }


    public function modifyPage (FrontmatterRepoPid $pid, string $instructions) {
        $tpl = new JobTemplate(__DIR__ . "/job-modify-text.txt");
        $page = $pid->get();

        $tpl->setData([
            "instructions" => $instructions,
            "links" => $this->targetRepo->getPageLinksAsMardownLinks($pid->getLang(), true),
        ]);
        $this->client->reset($tpl->getSystemContent(), 0.05, "gpt-4o");
        $this->client->getCache()->clear();
        $this->client->textComplete([
            $page->body,
            $tpl->getUserContent()
        ], streamer: function (LackOpenAiResponse $response) use ($page) {
            $page->body = $response->getTextCleaned();
            $this->targetRepo->storePage($page);
        });
    }



    public function translateMeta(FrontmatterRepoPid $pid)
    {
        $page = $pid->get();

        $headerTranslated = $this->client->getFacet()->promptData(__DIR__ . "/job-translate-meta.txt", [
            "lang" => $pid->getLang(),
            "input" => json_encode([
                "title" => $page->header["title"] ?? "",
                "description" => $page->header["description"] ?? "",
                "permalink" => $page->header["permalink"] ?? "",
                "short_description" => $page->header["short_description"] ?? "",
            ]),
        ], T_PageMeta::class);

        foreach ($headerTranslated as $key => $value) {
            $page->header[$key] = $value;
            if ($key === "permalink" && $value !== null)
                $page->header[$key] = $value;
        }
        $this->targetRepo->storePage($page);
    }

    public function translate(FrontmatterRepoPid $pid) {
        $lang = $pid->getLang();
        $tpl = new JobTemplate(__DIR__ . "/job-translate.txt");
        $page = $pid->get();


        $this->client->getCache()->clear();

        $tpl->setData([
            "lang" => $lang,
            "links" => $this->targetRepo->getPageLinksAsMardownLinks($lang, true),
            "content" => $page->body,
        ]);
        $this->client->reset($tpl->getSystemContent(), 0.05);
        $ret = $this->client->textComplete([
            $page->body,
            $tpl->getUserContent()
        ]);
        $page->body = $ret->getTextCleaned();
        $this->targetRepo->storePage($page);
    }



}
