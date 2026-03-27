<?php
declare(strict_types=1);
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    ob_start();
    chdir(public_path('pdv'));
    include public_path('pdv/login.php');
    return ob_get_clean();
});