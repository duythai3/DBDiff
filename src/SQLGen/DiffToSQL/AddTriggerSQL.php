<?php namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class AddTriggerSQL implements SQLGenInterface {

    function __construct($obj) {
        $this->obj = $obj;
    }
    
    public function getUp() {
        $trigger = $this->obj->trigger;
        $connection = $this->obj->connection;
        $res = $connection->select("SHOW CREATE TRIGGER `$trigger`");
        $stm = $res[0]['SQL Original Statement'].';';
        return "delimiter //\r\n$stm//\r\ndelimiter ;";
    }

    public function getDown() {
        $trigger = $this->obj->trigger;
        return "DROP TRIGGER `$trigger`;";
    }
}
