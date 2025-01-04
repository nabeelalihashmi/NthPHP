<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use Framework\Attributes\Route;

class FormController {
    #[Route(['POST'], '/submit')]
    public function submit() {
        return print_r($_POST, true);
    }
    
}