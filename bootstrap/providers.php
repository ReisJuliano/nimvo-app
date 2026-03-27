<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    App\Providers\TenancyServiceProvider::class,
    App\Providers\AdminRouteServiceProvider::class,
];
