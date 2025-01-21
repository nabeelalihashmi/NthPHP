<?php

namespace App\Middleware;

use Framework\Classes\Blade;
use Framework\HTTP\Responses\JSONResponse;
use Framework\HTTP\Responses\RedirectResponse;

class CSRFCheckMiddleware {
    public function handle($params = []) {
        if (!Blade::getInstance()->csrfIsValid()) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                return new JSONResponse(['success' => false, 'error' => 'CSRF', 'message' => 'Token is expired. Refresh the page.']);
            }
            $message = urlencode('Token is expired. Refresh the page.');
            return new RedirectResponse(BASEURL . '/error?message=' . $message);
        }

        return true;
    }
}
