<?php

namespace App\Ctrl;

use App\Business\Data\DataEditor;
use App\Business\Website2\Website2CreatorEditor;
use Brace\Router\Attributes\BraceRoute;
use http\Message\Body;
use Lack\Frontmatter\Repo\FrontmatterRepo;
use Lack\OpenAi\LackOpenAiClient;

class InitializeCtrl
{


    public function __construct(
        private FrontmatterRepo $frontmatterRepo,
        private FrontmatterRepo $templateRepo,
        private LackOpenAiClient $openai,
        private string $context,

    )
    {
    }



    #[BraceRoute("POST@/initialize/init()", "api.initialize.post")]
    public function initialize()
    {
        $tplRoot = $this->templateRepo->getRootPath()->withSubPath("_root")->asDirectory();
        $targetDir = CONF_PATH;

        $tplRoot->copyTo(phore_dir($targetDir));

        return ["ok" => true];
    }


    #[BraceRoute("POST@/initialize/ai_gen()", "api.initialize.ai_gen")]
    public function aiGenerate(array $query, array $body)
    {
        $file = $query["file"];
        $context = $body["context"];


        $targetDir = phore_dir(CONF_PATH);
        $file = $targetDir->withSubPath($file)->assertFile();

        $de = new DataEditor($context, $this->openai);
        $de->adjust($file);

        return ["ok" => true];
    }


}
