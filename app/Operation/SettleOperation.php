<?php declare(strict_types=1);
namespace DataTransfer\Operation;
use DataTransfer\Database\TrgtDB;
use DataTransfer\Exception\DataTransferException;
/**
 * Magic operation to treat target DB insert as a settlement operation. This operation will not generate any new columns, but will validate the existing columns and ensure that the data is settled correctly.
 */
class SettleOperation extends Operation{
    public string $table;
    public array $tableColumns;
    public function settle(string $table):self{
        $this->table = $table;
        return $this;
    }
    public static function from(Operation $previousOperation):self{
        $o = new self();
        $o->id = OperationManager::generateId();
        $o->previousOperation = $previousOperation;
        return $o;
    }
    public function getColumnsFromTable():void{
        $this->tableColumns = TrgtDB::selectColumns($this->table);
    }
    public function validateThenGenerateColumns():array{
        $columns = $this->previousOperation->tableConfig->columns;
        if(!self::columnExists($this->tableColumns,$columns)){
            throw new DataTransferException("Cannot settle to target table, column names seems to not match.");
        }
        return $this->tableColumns;
    }
    public function transform(iterable $data):iterable{
        foreach($data as $row){
            yield $row;
        }
    }
}