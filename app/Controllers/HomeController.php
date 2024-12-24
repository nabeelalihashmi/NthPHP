<?php

namespace App\Controllers;

use Framework\Attributes\Route;
use Framework\Classes\Blade;
use Framework\HTTP\Responses\JSONResponse;
use Framework\HTTP\Responses\RedirectResponse;

class HomeController {

    #[Route(['GET'], '/')]
    public function index() {
        return Blade::run('home');
        
    }

    #[Route(['GET'], '/json')]
    public function json() {
        return new JSONResponse(['message' => 'Hello, World!']);
    }

    #[Route(['GET'], '/redirect')]
    public function redirect() {
        return new RedirectResponse('https://aliveforms.com');
    }

    public function notFound() {
        return 'Not Found!';
    }

    public function methodNotAllowed() {
        return 'Not allowed!';
    }
}
