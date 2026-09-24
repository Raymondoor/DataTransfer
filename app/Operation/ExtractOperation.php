<?php declare(strict_types=1);
namespace DataTransfer\Operation;
class ExtractOperation extends Operation{
    public array $columns;
    /**
     * column to extract
     */
    public function extract(array|string $columns):self{
        $this->columns = is_string($columns) ? [$columns] : $columns;
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
        foreach($this->columns as $column){
            if(self::columnExists($columns, [$column]) === false){
                throw new \DataTransfer\Exception\DataTransferException("Column '$column' does not exist in the table.");
            }
        }
        return $this->columns;
    }
}