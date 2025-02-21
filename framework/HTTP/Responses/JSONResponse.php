<?php

namespace Framework\HTTP\Responses;

use Framework\HTTP\Response;

class JSONResponse extends Response {
    public function __construct(array|string $data, int $statusCode = 200, array $headers = []) {
        parent::__construct(json_encode($data), $statusCode, $headers);
        $this->setHeader('Content-Type', 'application/json');
    }
}
