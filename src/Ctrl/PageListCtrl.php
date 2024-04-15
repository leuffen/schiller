<?php

namespace App\Ctrl;

use App\Business\Website2\Website2CreatorEditor;
use Brace\Router\Attributes\BraceRoute;
use http\Message\Body;
use Lack\Frontmatter\Repo\FrontmatterRepo;
use Lack\OpenAi\LackOpenAiClient;

class PageListCtrl
{


    public function __construct(
        private FrontmatterRepo $frontmatterRepo,
        private FrontmatterRepo $templateRepo,
        private LackOpenAiClient $openai,
        private string $context,

    )
    {
    }


    #[BraceRoute("GET@/pages/list", "api.list")]
    public function list(array $query)
    {
        $lang = $query["lang"] ?? "de";
        return [
            "pages" => $this->frontmatterRepo->export("*", $lang),
        ];
    }



    #[BraceRoute("GET@/pages/getAvailLangs()", "api.langs.get")]
    public function getAvailLangs()
    {
        $langs = [];
        foreach ($this->frontmatterRepo->list() as $page) {
            $langs[] = $page->lang;
        }
        return array_values(array_unique($langs));

    }

    #[BraceRoute("POST@/pages/update()", "api.pid.post")]
    public function post(array $body)
    {
        $this->frontmatterRepo->import($body);
        return ["ok" => true];
    }

    #[BraceRoute("POST@/pages/create()", "api.pid.create")]
    public function create(array $query)
    {
        out($query);
        $fromTemplatePid = $query["templatePid"];
        $targetAliasPid = $query["aliasPid"] ?? $fromTemplatePid;
        $targetAliasPid = trim($targetAliasPid) == "" ? $fromTemplatePid : $targetAliasPid;

        $templatePid = $this->templateRepo->selectPid($fromTemplatePid, "de");
        $page = $templatePid->get();

        $newPid = $this->frontmatterRepo->selectPid($targetAliasPid, "de");
        $newPage = $newPid->create();
        $newPage->body = $page->body;
        $newPage->header = $page->header;
        $newPage->header["pid"] = $targetAliasPid;
        $newPage->header["_schiller_template"] = $fromTemplatePid;

        $this->frontmatterRepo->storePage($newPage);

        out(phore_uri($templatePid->getAbsoluteStoreUri())->getDirname()->withFileName("_section.yml"));

        // Copy the _section.yml if not existing
        $templateSectionYaml = phore_uri($templatePid->getAbsoluteStoreUri())->getDirname()->withFileName("_section.yml");
        $pageSectionYaml = phore_file($newPid->getAbsoluteStoreUri())->getDirname()->withFileName("_section.yml");

        if ($templateSectionYaml->exists() && ! $pageSectionYaml->exists()) {
            $templateSectionYaml->streamCopyTo($pageSectionYaml);
        }

        return ["ok" => true];
    }


    #[BraceRoute("POST@/pages/copyLanguageContent()", "api.pid.copyLanguageContent")]
    public function copyLanguageContent(array $query)
    {
        $pid = $query["pid"];
        $fromLang = $query["fromLang"];
        $toLang = $query["toLang"];

        $tplPage = $this->frontmatterRepo->selectPid($pid, $fromLang)->get();

        $newPage = $this->frontmatterRepo->selectPid($pid, $toLang)->create();
        $newPage->body = $tplPage->body;
        $newPage->header = $tplPage->header;
        $newPage->header["lang"] = $toLang;
        if ($newPage->header["permalink"] ?? null !== null)
            $newPage->header["permalink"] = "/$toLang" . $newPage->header["permalink"];

        $this->frontmatterRepo->storePage($newPage);
        return ["ok" => true];
    }

    #[BraceRoute("POST@/pages/translate()", "api.pid.translate")]
    public function translate(array $query)
    {
        $pid = $query["pid"];
        $lang = $query["lang"];

        $page = $this->frontmatterRepo->selectPid($pid, $lang);
        $w2c = new Website2CreatorEditor($this->context, $this->frontmatterRepo, $this->openai);
        $w2c->translate($page);

        return ["ok" => true];
    }
    #[BraceRoute("POST@/pages/translateMeta()", "api.pid.translateMeta")]
    public function translateMeta(array $query)
    {
        $pid = $query["pid"];
        $lang = $query["lang"];

        $page = $this->frontmatterRepo->selectPid($pid, $lang);
        $w2c = new Website2CreatorEditor($this->context, $this->frontmatterRepo, $this->openai);
        $w2c->translateMeta($page);

        return ["ok" => true];
    }

    #[BraceRoute("POST@/pages/copyContent()", "api.pid.copyContent")]
    public function copyContent(array $query)
    {
        $fromTemplatePid = $query["templatePid"];
        $targetPid = $query["pid"];

        $tplPage = $this->templateRepo->selectPid($fromTemplatePid, "de")->get();

        $newPage = $this->frontmatterRepo->selectPid($targetPid, "de")->get();
        $newPage->body = $tplPage->body;
        $newPage->header["_schiller_template"] = $fromTemplatePid;

        $this->frontmatterRepo->storePage($newPage);
        return ["ok" => true];
    }

    #[BraceRoute("POST@/pages/generate()", "api.pid.generate")]
    public function generate(array $query, )
    {
        set_time_limit(300);
        ignore_user_abort(true);

        $pid = $query["pid"];


        $page = $this->frontmatterRepo->selectPid($pid, "de");

        $w2c = new Website2CreatorEditor($this->context, $this->frontmatterRepo, $this->openai);
        $w2c->adjust($page);
        return ["ok" => true];
    }

      #[BraceRoute("POST@/pages/generateMeta()", "api.pid.generateMeta")]
    public function generateMeta(array $query, )
    {
        set_time_limit(300);

        $pid = $query["pid"];


        $page = $this->frontmatterRepo->selectPid($pid, "de");

        $w2c = new Website2CreatorEditor($this->context, $this->frontmatterRepo, $this->openai);
        $w2c->generateMeta($page);
        return ["ok" => true];
    }

          #[BraceRoute("POST@/pages/modifyPageByInstructions()", "api.pid.modifyPageByInstructions")]
    public function modifyPageByInstructions(array $query, array $body)
    {
        set_time_limit(300);
        out($query);
        $pid = $query["pid"];
        $lang = $query["lang"];
        $instructions = $body["instructions"];



        $page = $this->frontmatterRepo->selectPid($pid, $lang);

        $w2c = new Website2CreatorEditor($this->context, $this->frontmatterRepo, $this->openai);

        $w2c->modifyPage($page, $instructions);
        return ["ok" => true];
    }

}
