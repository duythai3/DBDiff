<?php namespace DBDiff\Diff;


class AddTrigger {

    function __construct($trigger, $connection) {
        $this->trigger = $trigger;
        $this->connection = $connection;
    }
}
