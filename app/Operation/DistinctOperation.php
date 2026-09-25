<?php declare(strict_types=1);
namespace DataTransfer\Operation;
class DistinctOperation extends Operation{
    public string $column;
    /**
     * distinct column of choise. can only be one column
     */
    public function distinct(string $column):self{
        $this->column = $column;
        $this->setTableConfiguration($this->id,$this->validateThenGenerateColumns());
        return $this;
    }
    public static function from(Operation $previousOperation):self{
        $o = new self();
        $o->id = OperationManager::generateId();
        $o->previousOperation = $previousOperation;
        return $o;
    }
    public function validateThenGenerateColumns():array{
        $columns = $this->previousOperation->tableConfig->columns;
        if(self::columnExists($columns, [$this->column]) === false){
            throw new \DataTransfer\Exception\DataTransferException("Column '$this->column' does not exist in the table.");
        }
        return [$this->column];
    }
    public function selectQueryFromPrevious(Operation $previous):string{
        return 'SELECT DISTINCT `'.$this->column.'` FROM `'.$previous->tableConfig->tablename.'`';
    }
    public function transform(iterable $data):iterable{
        foreach($data as $row){
            yield $row;
        }
    }
}