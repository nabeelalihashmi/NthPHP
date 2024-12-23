<?php

namespace Framework\Classes;

use eftec\bladeone\BladeOne;

class Blade {

    private static $_instance = null;

    public function __construct() {
        $views = DIR . '/app/Views';
        $cache = DIR . '/cache/compiled';
        self::$_instance = new BladeOne($views, $cache, BladeOne::MODE_AUTO);
    }
    public static function instance() {

        if (self::$_instance == null) {
            new self();
        }

        return self::$_instance;
    }

    public static function run($view, $vars = []) {
        return self::instance()->run($view, $vars);
    }
}
