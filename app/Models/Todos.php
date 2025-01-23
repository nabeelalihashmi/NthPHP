<?php

namespace App\Models;

use Exception;
use Framework\Classes\RB;
use RedBeanPHP\SimpleModel;

$lifeCycle = '';
class Todos extends SimpleModel {
    public function open() {
       global $lifeCycle;
       $lifeCycle .= "called open: ".$this->id;
    }
    public function dispense() {
        global $lifeCycle;
        $lifeCycle .= "called dispense() ".$this->bean;
    }
    public function update() {
        $todo = RB::findOne('todos', 'title = ?', [$this->bean->title]);
        if ($todo && $todo->id != $this->bean->id) {
            throw new Exception('Title already exists');
        }
        global $lifeCycle;
        $lifeCycle .= "called update() ".$this->bean;
    }
    public function after_update() {
        global $lifeCycle;
        $lifeCycle .= "called after_update() ".$this->bean;
    }
    public function delete() {
        global $lifeCycle;
        $lifeCycle .= "called delete() ".$this->bean;
    }
    public function after_delete() {
        global $lifeCycle;
        $lifeCycle .= "called after_delete() ".$this->bean;
    }

    public function __destruct()
    {
        global $lifeCycle;
        $lifeCycle .= "called __destruct()";
        echo $lifeCycle;
    }
}
