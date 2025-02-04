<?php

namespace Framework\Classes;

use Delight\Auth\Auth as DelightAuth;
use Delight\Db\PdoDatabase;
use Delight\Db\PdoDsn;

class Auth {
    private static ?DelightAuth $instance = null;
    
    public static function getInstance() {
        if (!isset(self::$instance)) {
            
            $host = cfg('database.host');
            $name = cfg('database.name');
            $dsn = "mysql:host={$host};dbname={$name}";
            $db = PdoDatabase::fromDsn(new PdoDsn($dsn, cfg('database.username'), cfg('database.password')));
            self::$instance = new DelightAuth($db, null, null, false);
        }
        return self::$instance;
    }
}