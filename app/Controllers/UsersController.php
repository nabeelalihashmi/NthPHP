<?php

namespace App\Controllers;

use App\Middleware\LoginCheckMiddleware;
use Framework\Attributes\Crud;
use Framework\HTTP\Controllers\CrudController;

#[Crud(
    enabledMethods: ['index', 'show', 'create', 'update', 'delete'],
    path: '/users',
    middlewares: ['create' => LoginCheckMiddleware::class]
)]
class UsersController extends CrudController {
    public function __construct() {
        parent::__construct('users', ['name', 'email', 'password']);
    }
}
