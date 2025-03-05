<?php


namespace App\Controllers;

use Fiber;
use Framework\Attributes\Route;
use Framework\HTTP\Responses\JSONResponse;
use Tracy\Debugger;

class FiberTestController {
    function heavyFunction() {
        sleep(4);
        Debugger::log('Heavy function done!');
    }
    #[Route(['GET'], '/fibertest')]
    public function test() {
        $fiber = new Fiber(function () {
            $this->heavyFunction();
        });
    
        $pausedData = $fiber->start();
    
        return new JSONResponse([
            'paused' => $pausedData,
        ]);
    }
}