<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['status' => 'ok']));

require base_path('./routes/api/auth.php');
require base_path('./routes/api/profile.php');
require base_path('./routes/api/journals.php');
require base_path('./routes/api/social.php');
require base_path('./routes/api/places.php');
require base_path('./routes/api/search.php');
