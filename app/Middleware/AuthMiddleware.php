<?php

namespace App\Middleware;

class AuthMiddleware {
    public function handle() {
        if (!isset($_GET['auth'])) {
            return 'Authentication required';
        }
        return true;
    }
}
