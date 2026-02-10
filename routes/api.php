<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['status' => 'ok']));

require base_path('./routes/api/auth.php');
require base_path('./routes/api/profile.php');
