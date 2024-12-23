<?php

namespace Framework\HTTP\Responses;

use Framework\HTTP\Response;

class RedirectResponse extends Response {
    public function __construct(string $url, int $statusCode = 302, array $headers = []) {
        parent::__construct('', $statusCode, $headers);

        $this->setHeader('Location', $url);

        $this->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
        $this->setHeader('Pragma', 'no-cache');
    }
}
