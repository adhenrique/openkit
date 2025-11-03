<?php

use Illuminate\Support\Facades\Route;
use OpenKit\Http\Controllers\DocsController;

$prefix = config('openkit.path', 'openkit');

Route::prefix($prefix)->group(function () {
    Route::get('/docs', [DocsController::class, 'showUi'])->name('openkit.ui');
});
