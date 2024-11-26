<?php namespace DBDiff\DB\Schema;

use Diff\Differ\ListDiffer;

use DBDiff\Params\ParamsFactory;
use DBDiff\Diff\SetDBCollation;
use DBDiff\Diff\SetDBCharset;
use DBDiff\Diff\DropTable;
use DBDiff\Diff\AddTable;
use DBDiff\Diff\AlterTable;



class DBTrigger {

    function __construct($manager) {
        $this->manager = $manager;
    }

    function getDiff() {
        $params = ParamsFactory::get();

        $diffs = [];

        //
        $sourceDBName = $this->manager->getDB('source')->getDatabaseName();
        $targetDBName = $this->manager->getDB('target')->getDatabaseName();

        $sourceTriggers = $this->getTriggers('source', $sourceDBName);
        $targetTriggers = $this->getTriggers('target', $targetDBName);


        return $diffs;
    }

    /**
     * Get all triggers from a database
     * @param $connection
     * @param $dbName
     * @return array
     */
    private function getTriggers($connection, $dbName) {
        $result = $this->manager->getDB($connection)->select("select TRIGGER_CATALOG, TRIGGER_SCHEMA, TRIGGER_NAME, 
                EVENT_MANIPULATION, EVENT_OBJECT_CATALOG, EVENT_OBJECT_SCHEMA, EVENT_OBJECT_TABLE, ACTION_ORDER,
                ACTION_CONDITION, ACTION_STATEMENT, ACTION_ORIENTATION, ACTION_TIMING, ACTION_REFERENCE_OLD_TABLE,
                ACTION_REFERENCE_NEW_TABLE, ACTION_REFERENCE_OLD_ROW, ACTION_REFERENCE_NEW_ROW, CREATED, SQL_MODE,
                DEFINER, CHARACTER_SET_CLIENT, COLLATION_CONNECTION, DATABASE_COLLATION from information_schema.TRIGGERS where TRIGGER_SCHEMA='$dbName'");
        return $result;
    }

}
