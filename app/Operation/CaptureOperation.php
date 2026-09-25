<?php declare(strict_types=1);
namespace DataTransfer\Operation;

use DataTransfer\Database\SrcDB;
/**
 * Magic operation to treat source DB as a previous operation. This operation will replicate the source DB table to a new stage ensuring the modification does not happen in source.
 */
class CaptureOperation extends Operation{
    public string $table;
    public array $tableColumns;
    public function capture(string $table):self{
        $this->table = $table;
        return $this;
    }
    public static function fromTable(string $tablename):self{
        $o = new self();
        $o->id = OperationManager::generateId();
        $o->capture($tablename);
        $o->tableColumns = SrcDB::selectColumns($o->table);
        $o->setTableConfiguration($o->id,$o->validateThenGenerateColumns());
        return $o;
    }
    public static function fromColumns(string $tablename, array $columns):self{
        $o = new self();
        $o->id = OperationManager::generateId();
        $o->capture($tablename);
        $o->tableColumns = $columns;
        $o->setTableConfiguration($o->id,$o->validateThenGenerateColumns());
        return $o;
    }
    public function getColumnsFromTable():void{
        // get columns from table
        $this->tableColumns = [];
    }
    public function validateThenGenerateColumns():array{
        return $this->tableColumns;
    }
    public function selectQueryFromPrevious(?Operation $previous):string{
        return 'SELECT * FROM `'.$this->table.'`';
    }
    public function transform(iterable $data):iterable{
        foreach($data as $row){
            yield $row;
        }
    }
}