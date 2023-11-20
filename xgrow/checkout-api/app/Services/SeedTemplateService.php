<?php

namespace App\Services;

use App\Section;
use App\Content;
use Illuminate\Support\Facades\Auth;
use DB;

class SeedTemplateService
{
    protected $section;
    protected $sectionId;
    protected $content;
    protected $platform_id;

    public function __construct()
    {
        $this->platform_id = Auth::user()->platform_id;
    }

    private function searchSection()
    {
        $this->section = Section::where('platform_id', $this->platform_id)
            ->select('name')
            ->where('id', $this->sectionId)
            ->get();
    }

    public function getSection($sectionId)
    {
        $this->sectionId = $sectionId;
        $this->searchSection();
        return $this->section;
    }

    public function searchContent()
    {
        $this->content = DB::table('sections')
            ->join('contents', 'sections.id', '=', 'contents.section_id')
            ->join('files', 'contents.thumb_small_id', '=', 'files.id')
            ->select('sections.name', 'sections.description', 'contents.id', 'contents.thumb_small_id','contents.content_html','contents.subtitle','contents.description', 'files.filename')
            ->where('sections.platform_id', $this->platform_id)
            ->where('sections.id', $this->sectionId)
            ->get();
    }

    public function getContent($sectionId)
    {
        $this->sectionId = $sectionId;
        $this->searchContent();
        return $this->content;
    }


}
