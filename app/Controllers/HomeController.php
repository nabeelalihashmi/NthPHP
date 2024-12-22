<?php

namespace App\Controllers;

use Framework\Attributes\Route;
use Framework\HTTP\Responses\JSONResponse;

class HomeController {

    #[Route(['GET'], '/')]
    public function index() {
        return new JSONResponse(['message' => 'Hello, World!']);
    } 
}
