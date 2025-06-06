<?php

namespace App\Controllers;

use App\Middleware\LoginCheckMiddleware;
use Framework\Attributes\Route;
use Framework\Classes\Blade;
use Framework\Classes\Defer;
use Framework\HTTP\Responses\JSONResponse;
use Framework\HTTP\Responses\RedirectResponse;
use RedBeanPHP\R;

class HomeController {


    #[Route(['GET'], '/hello/{str}')]
    public function hello($str) {
        Defer::defer(function() use ($str) {
            var_dump($str);
        });

        return 'Hello ' . $str;
    }
    

    #[Route(['GET'], '/')]
    public function index() {
        return Blade::view('home');
    }

    #[Route(['GET'], '/json')]
    public function json() {
        return new JSONResponse(['message' => 'Hello, World!']);
    }

    #[Route(['GET'], '/json/{id}')]
    public function jsonId($id) {
        return new JSONResponse(['message' => 'Hello,'. $id]);
    }

    #[Route(['GET'], '/user/{username}')]
    public function getUser($username) {
        return new JSONResponse($username);
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

    #[Route(['GET'], '/db')]
    public function testDb() {
        $todo = R::dispense('todos');
        $todo->title = 'Learn NthPHP';
        $todo->created_at = R::isoDateTime();
        $id = R::store($todo);

        var_dump($id);
        $todo = R::load('todo', $id);
        return json_encode($todo);

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

    #[Route(['POST'], '/ajax')]
    public function postAjax() {
        return new JSONResponse(['success' => true, 'message' => 'Hello, ' . $_POST['first_name']]);
    }

    public function fileRoute() {
        return new JSONResponse(['success' => true]);
    }

    #[Route(['GET'], '/defer' )]
    public function defer() {
        Defer::defer(function() {
            sleep(4);
            echo 'Something here!';
        });
        return "Done!";
    }
}
