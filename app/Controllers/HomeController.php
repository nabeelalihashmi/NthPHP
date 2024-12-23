<?php

namespace App\Controllers;

use Framework\Attributes\Route;
use Framework\Classes\Blade;

class HomeController {

    #[Route(['GET'], '/')]
    public function index() {
        echo Blade::run('home');
    }


    public function notFound() {
        echo 'Not Found!';
    }

    public function methodNotAllowed() {
        echo 'Not allowed!';
    }
}
