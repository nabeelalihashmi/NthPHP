<?php

namespace Framework\HTTP\Responses;

use Framework\HTTP\Response;

class RedirectResponse extends Response
{
    private $url;
    private $status;

    public function __construct(string $url, int $status = 302)
    {
        $this->url = $url;
        $this->status = $status;
    }

    public function send()
    {
        header('Location: ' . $this->url, true, $this->status);
        exit;
    }
}
