<?php

namespace Framework\HTTP;

abstract class Response {
    protected $content;
    protected $statusCode;
    protected $headers = [];

    public function __construct($content = '', $statusCode = 200, $headers = []) {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function setStatusCode($statusCode) {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function setHeader($name, $value) {
        $this->headers[$name] = $value;
        return $this;
    }

    public function send($response = null) {
        if ($response instanceof \Swoole\Http\Response) {
            $this->sendToSwoole($response);
        } else {
            $this->sendToPhp();
        }
    }

    private function sendToPhp() {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        echo $this->content;
    }

    private function sendToSwoole(\Swoole\Http\Response $response) {
        $response->status($this->statusCode);

        foreach ($this->headers as $name => $value) {
            $response->header($name, $value);
        }

        $response->end($this->content);
    }
}
