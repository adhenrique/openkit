<?php

namespace OpenKit\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class DocsController
{
    public function showUi(): View|Application|Factory
    {
        $jsonUrl = asset(config('openkit.json_file_name', 'openapi.json'));
        $title = config('openkit.ui.title', 'API Docs');
        $cdnUrl = config('openkit.ui.cdn_url');

        return view('openkit::index', compact('jsonUrl', 'title', 'cdnUrl'));
    }
}
