<?php
namespace Framework\Classes;

use Hashids\Hashids;

class HashId {
    private static $_instance = null;


    public function __construct()
    {
      self::$_instance = new Hashids(Config::get('app_key'), 16);
    }
    public static function instance() {

        if (self::$_instance == null) {
            new self();
        }
        return self::$_instance;
    }
    
}