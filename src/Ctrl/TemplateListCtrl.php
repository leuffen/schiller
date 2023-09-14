<?php

namespace App\Ctrl;

use Brace\Router\Attributes\BraceRoute;
use Lack\Frontmatter\Repo\FrontmatterRepo;

class TemplateListCtrl
{


    public function __construct(
        private FrontmatterRepo $templateRepo
    )
    {
    }


    #[BraceRoute("GET@/templates/list", "templates.list")]
    public function list()
    {
        $templates = $this->templateRepo->export("*");
        $ret = [];
        foreach ($templates as $template) {
            $ret[] = [
                "value" => $template["pid"],
                "text" => $template["pid"] . "(" . ($template["_schiller_desc"] ?? '') . ")"
            ];
        }
        return [
            "templates" => $ret
        ];
    }


}
