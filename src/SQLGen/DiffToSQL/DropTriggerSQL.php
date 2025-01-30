<?php namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class DropTriggerSQL implements SQLGenInterface {

    function __construct($obj) {
        $this->obj = $obj;
    }

    public function getUp() {
        $trigger = $this->obj->trigger;
        return "DROP TRIGGER `$trigger`;";
    }

    public function getDown() {
        return "";
    }
}
