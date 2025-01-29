<?php

namespace App\Middleware;

use Framework\Classes\Auth;
use Framework\Classes\Blade;
use Framework\HTTP\Responses\JSONResponse;
use Framework\HTTP\Responses\RedirectResponse;

class LoginCheckMiddleware {
    public function handle() {
        if(Auth::getInstance()->isSuspended()) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                return new JSONResponse(['success' => false, 'error' => 'AUTH', 'message' => 'This account has been suspended.']);
            }
            return Blade::view('_auth.message', ['heading' => 'This account has been suspended.', 'message' => '<a href="'. BASEURL .'/logout">Logout</a>']);
        }
        if (Auth::getInstance()->isLoggedIn() === false) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                return new JSONResponse(['success' => false, 'error' => 'AUTH', 'message' => 'user not logged in']);
            }
            return new RedirectResponse(BASEURL  . '/login');
        }

        return true;
    }
}
