<?php namespace DBDiff\Diff;


class UpdatedTrigger {

    function __construct($trigger, $connection) {
        $this->trigger = $trigger;
        $this->connection = $connection;
    }
}
