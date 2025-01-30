<?php namespace DBDiff\Diff;


class DropTrigger {

    function __construct($trigger, $connection) {
        $this->trigger = $trigger;
        $this->connection = $connection;
    }
}
