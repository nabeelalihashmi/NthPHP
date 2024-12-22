<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use Framework\Attributes\Route;
use Framework\HTTP\Responses\JSONResponse;

class HomeController {

    #[Route(['GET'], '/')]
    public function index() {
        return new JSONResponse(['message' => 'Hello, World!']);
    } 
    
    #[Route(['GET', 'POST'], '/user/{name}', [AuthMiddleware::class])]
    public function user($username) {
        (new JSONResponse(['message' => 'Hello, ' . $username]))->send();
    }
}
