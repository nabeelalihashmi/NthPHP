<?php

namespace Framework\HTTP\Responses;

use Framework\HTTP\Response;

class FileResponse extends Response {
    protected $filePath;

    public function __construct($filePath) {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $this->filePath = $filePath;
    }

    public function send() {
        header('Content-Type: ' . mime_content_type($this->filePath));
        header('Content-Disposition: attachment; filename="' . basename($this->filePath) . '"');
        header('Content-Length: ' . filesize($this->filePath));

        readfile($this->filePath);
    }
}
