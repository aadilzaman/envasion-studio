<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Fallback route that serves the Vue 3 Single Page Application (SPA)
| for all non-API requests, allowing client-side routing.
|
*/

Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api).*$');
