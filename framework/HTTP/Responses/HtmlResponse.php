<?php

namespace Framework\HTTP\Responses;

use Framework\HTTP\Response;

class HtmlResponse extends Response
{
    private $content;
    private $status;

    public function __construct(string $content, int $status = 200)
    {
        $this->content = $content;
        $this->status = $status;
    }

    public function send()
    {
        header('Content-Type: text/html', true, $this->status);
        echo $this->content;
    }
}
