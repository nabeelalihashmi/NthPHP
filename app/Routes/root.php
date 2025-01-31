<?php

use App\Controllers\HomeController;

return [
    [
        'method' => 'GET',
        'path' => '/test',
        'handler' => [HomeController::class, 'fileRoute'],
        'middleware' => []
    ]
];