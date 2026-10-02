<?php declare(strict_types=1);
namespace DataTransfer\Operation;
/**
 * Add new columns to schema, the values inside will default to null
 */
class AddColumnsOperation extends Operation{
    public array $columns = [];
    public function add(array|string $columns):self{
        $this->columns = is_string($columns) ? [$columns] : $columns;
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
        $existingColumn = self::columnExists($this->columns, $columns); 
        if($existingColumn !== false){
            throw new \DataTransfer\Exception\DataTransferValueException("Column '$existingColumn' already exists in the table.");
        }
        array_push($columns, ...$this->columns);
        return $columns;
    }
    public function transform(iterable $data):iterable{
        foreach($data as $row){
            foreach($this->columns as $column){
                $row[$column] = null;
            }
            yield $row;
        }
    }
}