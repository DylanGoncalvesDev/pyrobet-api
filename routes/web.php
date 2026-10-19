<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'PyroBet API is fully operational',
        'status'  => 'online',
        'documentation_url' => 'http://localhost:8000/api/documentation#/'
    ], 200);
});
