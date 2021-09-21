<?php

use SaTan\Dcat\EnvHelper\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::resource('satan/env', Controllers\DcatEnvController::class);
