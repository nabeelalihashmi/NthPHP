<?php

namespace Framework\HTTP;

class Request
{
    private $method;
    private $uri;
    private $queryParams;
    private $bodyParams;
    private $headers;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->queryParams = $_GET;
        $this->bodyParams = $this->parseBody();
        $this->headers = getallheaders();
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getQueryParam($key, $default = null)
    {
        return $this->queryParams[$key] ?? $default;
    }

    public function getQueryParams()
    {
        return $this->queryParams;
    }

    public function getBodyParam($key, $default = null)
    {
        return $this->bodyParams[$key] ?? $default;
    }

    public function getBodyParams()
    {
        return $this->bodyParams;
    }

    public function getHeader($key)
    {
        $key = strtolower($key);
        return $this->headers[$key] ?? null;
    }

    public function isAjax()
    {
        return $this->getHeader('X-Requested-With') === 'XMLHttpRequest';
    }

    private function parseBody()
    {
        if ($this->getHeader('Content-Type') === 'application/json') {
            return json_decode(file_get_contents('php://input'), true) ?? [];
        }
        return $_POST;
    }
}
