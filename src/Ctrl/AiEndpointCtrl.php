<?php

namespace App\Ctrl;

use App\Business\Suggests\SmartChange;
use Brace\Router\Attributes\BraceRoute;
use Lack\Frontmatter\Repo\FrontmatterRepo;
use Lack\OpenAi\LackOpenAiClient;

class AiEndpointCtrl
{
    public function __construct(
        private FrontmatterRepo $frontmatterRepo,
        private FrontmatterRepo $templateRepo,
        private LackOpenAiClient $openai,
        private string $context,

    )
    {
    }


    #[BraceRoute("POST@/ai/generate_change_request()", "api.ai.generate_change_request")]
    public function generateChangeRequest(array $body) {
        $instructions = $body["instructions"];

        $sm = new SmartChange("", $this->frontmatterRepo, $this->openai);

        return $sm->generateChangeRequest($instructions);
    }


}
