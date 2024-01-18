<?php namespace DBDiff\DB\Data;

use DBDiff\Params\ParamsFactory;
use DBDiff\Diff\SetDBCollation;
use DBDiff\Exceptions\DataException;
use DBDiff\Logger;


class DBData {

    function __construct($manager) {
        $this->manager = $manager;
    }

    function getDiff() {
        $params = ParamsFactory::get();

        $diffSequence = [];

        // Tables
        $tableData = new TableData($this->manager);

        $sourceTables = $params->tablesToScanForData;
        $targetTables = $this->manager->getTables('target');

        // when scanning for differences in data, only scan tables specified in the config file

        // for scanning all tables except ignore tables, use code below
        /*
        $sourceTables = $this->manager->getTables('source');
        if (isset($params->tablesToIgnore)) {
            $sourceTables = array_diff($sourceTables, $params->tablesToIgnore);
            $targetTables = array_diff($targetTables, $params->tablesToIgnore);
        }
        */

        $commonTables = array_intersect($sourceTables, $targetTables);
        foreach ($commonTables as $table) {
            try {
                $diffs = $tableData->getDiff($table);
                $diffSequence = array_merge($diffSequence, $diffs);
            } catch (DataException $e) {
                Logger::error($e->getMessage());
            }
        }

        /*
        $addedTables = array_diff($sourceTables, $targetTables);
        foreach ($addedTables as $table) {
            $diffs = $tableData->getNewData($table);
            $diffSequence = array_merge($diffSequence, $diffs);
        }

        $deletedTables = array_diff($targetTables, $sourceTables);
        foreach ($deletedTables as $table) {
            $diffs = $tableData->getOldData($table);
            $diffSequence = array_merge($diffSequence, $diffs);
        }
        */

        return $diffSequence;
    }

}
