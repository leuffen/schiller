<?php

namespace App\Business\Suggests;

use Lack\Frontmatter\Repo\FrontmatterRepo;
use Lack\OpenAi\Helper\JobTemplate;
use Lack\OpenAi\LackOpenAiClient;

class SmartChange
{
    public function __construct(
        public string $context,
        public FrontmatterRepo $targetRepo,
        public LackOpenAiClient $client
    ){

    }

    public function generateChangeRequest(string $instructions) : array {
        $tpl = new JobTemplate(__DIR__ . "/job-create-change-request.txt");

        $tpl->setData([
            "pages" => $this->targetRepo->getPagePidLinksAsMardownLinks("de"),
            "changes" => $instructions,
        ]);
        $this->client->reset($tpl->getSystemContent(), 0.1, "gpt-4-turbo-preview");

        //$this->client->getCache()->clear();
        return $this->client->textComplete([
            $tpl->getUserContent()
        ])->getJson();
    }

}
