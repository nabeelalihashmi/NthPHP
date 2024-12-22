<?php

namespace Framework\HTTP\Responses;

use Framework\HTTP\Response;

class JSONResponse extends Response
{
    private $data;
    private $status;

    public function __construct(array $data, int $status = 200)
    {
        $this->data = $data;
        $this->status = $status;
    }

    public function send()
    {
        header('Content-Type: application/json', true, $this->status);
        echo json_encode($this->data);
    }
}
