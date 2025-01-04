<?php

namespace App\Controllers;

use Framework\Attributes\Route;
use Framework\Classes\Blade;
use Framework\HTTP\Responses\JSONResponse;
use Framework\HTTP\Responses\RedirectResponse;

class HomeController {


    #[Route(['GET'], '/hello/{str}')]
    public function hello($str) {
        return 'Hello ' . $str;
    }
    

    #[Route(['GET'], '/')]
    public function index() {
        return Blade::run('home');
    }

    #[Route(['GET'], '/json')]
    public function json() {
        return new JSONResponse(['message' => 'Hello, World!']);
    }

    #[Route(['GET'], '/redirect')]
    public function redirect() {
        return new RedirectResponse('https://aliveforms.com');
    }

    #[Route(['GET', 'POST'], '/echo-request')]
    public function echoRequest() {
        return json_encode($_REQUEST);
    }

    public function notFound() {
        return '404|Not Found!';
    }

    public function methodNotAllowed() {
        return 'Not allowed!';
    }

    #[Route(['GET'], '/sse')]
    public function handleSSE() {

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        while (ob_get_level()) {
            ob_end_clean();
        }

        while (true) {

            $data = json_encode([
                'message' => 'Hello, this is an SSE message',
                'timestamp' => time(),
            ]);

            echo "data: {$data}\n\n";

            ob_flush();
            flush();

            if (connection_aborted()) {
                break;
            }

            sleep(1);
        }
    }
}
