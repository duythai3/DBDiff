<?php namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class UpdatedTriggerSQL implements SQLGenInterface {

    function __construct($obj) {
        $this->obj = $obj;
    }
    
    public function getUp() {
        $trigger = $this->obj->trigger;
        $connection = $this->obj->connection;
        $res = $connection->select("SHOW CREATE TRIGGER `$trigger`");
        $stm = $res[0]['SQL Original Statement'].';';
        return "DROP TRIGGER `$trigger`;\r\ndelimiter //\r\n$stm//\r\ndelimiter ;";
    }

    public function getDown() {
        return "";
    }
}
