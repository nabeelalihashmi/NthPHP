<?php

namespace Framework\HTTP\Responses;

use Framework\HTTP\Response;

class HtmlResponse extends Response {
    public function __construct(array $data, int $statusCode = 200, array $headers = []) {
        parent::__construct($data, $statusCode, $headers);
        $this->setHeader('Content-Type', 'text/html');
    }
}
