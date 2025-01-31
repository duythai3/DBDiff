<?php namespace DBDiff\DB\Schema;

use DBDiff\Diff\AddTrigger;
use DBDiff\Diff\DropTrigger;
use DBDiff\Diff\UpdatedTrigger;
use DBDiff\Logger;
use Diff\Differ\ListDiffer;

use DBDiff\Params\ParamsFactory;
use DBDiff\Diff\SetDBCollation;
use DBDiff\Diff\SetDBCharset;
use DBDiff\Diff\DropTable;
use DBDiff\Diff\AddTable;
use DBDiff\Diff\AlterTable;



class DBSchema {

    function __construct($manager) {
        $this->manager = $manager;
    }

    function getDiff() {
        $params = ParamsFactory::get();

        $diffs = [];

        // Tables
        $tableSchema = new TableSchema($this->manager);
        $sourceTables = $this->manager->getTables('source');
        $targetTables = $this->manager->getTables('target');
        if (isset($params->tablesToIgnore)) {
            $sourceTables = array_diff($sourceTables, $params->tablesToIgnore);
            $targetTables = array_diff($targetTables, $params->tablesToIgnore);
        }
        //
        $addedTables = array_diff($sourceTables, $targetTables);
        foreach ($addedTables as $table) {
            $diffs[] = new AddTable($table, $this->manager->getDB('source'));
        }
        //
        $commonTables = array_intersect($sourceTables, $targetTables);
        foreach ($commonTables as $table) {
            $tableDiff = $tableSchema->getDiff($table);
            $diffs = array_merge($diffs, $tableDiff);
        }

        // Collation
        $dbName = $this->manager->getDB('target')->getDatabaseName();
        $sourceCollation = $this->getDBVariable('source', 'collation_database');
        $targetCollation = $this->getDBVariable('target', 'collation_database');
        if ($sourceCollation !== $targetCollation) {
            $diffs[] = new SetDBCollation($dbName, $sourceCollation, $targetCollation);
        }

        // Charset
        $sourceCharset = $this->getDBVariable('source', 'character_set_database');
        $targetCharset = $this->getDBVariable('target', 'character_set_database');
        if ($sourceCharset !== $targetCharset) {
            $diffs[] = new SetDBCharset($dbName, $sourceCharset, $targetCharset);
        }

        // drop table
        $deletedTables = array_diff($targetTables, $sourceTables);
        foreach ($deletedTables as $table) {
            $diffs[] = new DropTable($table, $this->manager->getDB('target'));
        }

        // triggers
        $sourceTriggers = $this->manager->getTriggers('source');
        $targetTriggers = $this->manager->getTriggers('target');
        $newTriggerNames = $this->getNewTriggers($sourceTriggers, $targetTriggers);
        $updatedTriggerNames = $this->getUpdatedTriggers($sourceTriggers, $targetTriggers);
        foreach ($newTriggerNames as $newTriggerName) {
            $diffs[] = new AddTrigger($newTriggerName, $this->manager->getDB('source'));
        }
        foreach ($updatedTriggerNames as $updatedTriggerName) {
            $diffs[] = new UpdatedTrigger($updatedTriggerName, $this->manager->getDB('source'));
        }
        $deletedTriggers = $this->getDropTriggers($sourceTriggers, $targetTriggers);
        foreach ($deletedTriggers as $trigger) {
            Logger::info("Deleted trigger:$trigger");
            $diffs[] = new DropTrigger($trigger, $this->manager->getDB('target'));
        }

        //
        return $diffs;
    }

    protected function getDBVariable($connection, $var) {
        $result = $this->manager->getDB($connection)->select("show variables like '$var'");
        return $result[0]['Value'];
    }

    protected function getNewTriggers($sourceTriggers, $targetTriggers) {
        $sourceTriggerNames = [];
        $targetTriggerNames = [];
        foreach ($sourceTriggers as $trigger) {
            $sourceTriggerNames[] = $trigger['trigger_name'];
        }
        foreach ($targetTriggers as $trigger) {
            $targetTriggerNames[] = $trigger['trigger_name'];
        }
        return array_diff($sourceTriggerNames, $targetTriggerNames);
    }

    protected function getUpdatedTriggers_old($sourceTriggers, $targetTriggers) {
        $sourceTriggerNames = [];
        $targetTriggerNames = [];
        $sourceActionStatements = [];
        $targetActionStatements = [];
        foreach ($sourceTriggers as $trigger) {
            $triggerName = $trigger['trigger_name'];
            $sourceTriggerNames[] = $triggerName;
            $res = $this->manager->getDB('source')->select("SHOW CREATE TRIGGER `$triggerName`");
            $stm = $res[0]['SQL Original Statement'];
            $sourceActionStatements[$triggerName] = $stm;
        }
        foreach ($targetTriggers as $trigger) {
            $triggerName = $trigger['trigger_name'];
            $targetTriggerNames[] = $triggerName;
            $res = $this->manager->getDB('target')->select("SHOW CREATE TRIGGER `$triggerName`");
            $stm = $res[0]['SQL Original Statement'];
            $targetActionStatements[$triggerName] = $stm;
        }
        $commonTriggerNames = array_intersect($sourceTriggerNames, $targetTriggerNames);
        $updatedTriggerNames = [];
        foreach ($commonTriggerNames as $triggerName) {
            $sourceActionStatement = strtolower($sourceActionStatements[$triggerName]);
            $targetActionStatement = strtolower($targetActionStatements[$triggerName]);
            if ($sourceActionStatement !== $targetActionStatement) {
                $updatedTriggerNames[] = $triggerName;
            }
        }
        return $updatedTriggerNames;
    }

    protected function getUpdatedTriggers($sourceTriggers, $targetTriggers) {
        $sourceTriggerNames = [];
        $targetTriggerNames = [];
        $sourceActionStatements = [];
        $targetActionStatements = [];
        foreach ($sourceTriggers as $trigger) {
            $triggerName = $trigger['trigger_name'];
            $sourceTriggerNames[] = $triggerName;
            $sourceActionStatements[$triggerName] = $trigger['action_statement'];
        }
        foreach ($targetTriggers as $trigger) {
            $triggerName = $trigger['trigger_name'];
            $targetTriggerNames[] = $triggerName;
            $targetActionStatements[$triggerName] = $trigger['action_statement'];
        }
        $commonTriggerNames = array_intersect($sourceTriggerNames, $targetTriggerNames);
        $updatedTriggerNames = [];
        foreach ($commonTriggerNames as $triggerName) {
            $sourceActionStatement = strtolower($sourceActionStatements[$triggerName]);
            $targetActionStatement = strtolower($targetActionStatements[$triggerName]);
            $sourceActionStatement = preg_replace('/\s+/', '', $sourceActionStatement);
            $targetActionStatement = preg_replace('/\s+/', '', $targetActionStatement);

            if (strcmp($targetActionStatement, $targetActionStatement) !== 0) {
                Logger::info('var_dump:');
                var_dump($sourceActionStatement);
                var_dump($targetActionStatement);
                Logger::info("updated trigger:$triggerName");
                Logger::info("sourceActionStatement:$sourceActionStatement");
                Logger::info("targetActionStatement:$targetActionStatement");
                $updatedTriggerNames[] = $triggerName;
            }
        }
        return $updatedTriggerNames;
    }

    protected function strdiff($a,$b){
        $a = explode(" ", $a);
        $b = explode(" ", $b);
        $c = array_diff($a,$b);
        return implode(":", $c);

    }

    protected function getDropTriggers($sourceTriggers, $targetTriggers) {
        $sourceTriggerNames = [];
        $targetTriggerNames = [];
        foreach ($sourceTriggers as $trigger) {
            $triggerName = $trigger['trigger_name'];
            $sourceTriggerNames[] = $triggerName;
        }
        foreach ($targetTriggers as $trigger) {
            $triggerName = $trigger['trigger_name'];
            $targetTriggerNames[] = $triggerName;
        }
        return array_diff($targetTriggerNames, $sourceTriggerNames);
    }

}
