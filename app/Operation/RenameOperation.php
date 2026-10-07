<?php declare(strict_types=1);
namespace R3T\Operation;
use R3T\Exception\R3TException;
class RenameOperation extends Operation{
    public array $modifications;
    /**
     * list of columns to rename `[oldName => newName]`
     * @param string[] $modifications
     */
    public function rename(array $modifications):self{
        $this->modifications = $modifications;
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
        foreach($this->modifications as $oldName => $newName){
            if(!in_array($oldName, $columns)){
                throw new \R3T\Exception\R3TValueException("Column $oldName does not exist in the original columns");
            }
            if(in_array($newName, $columns)){
                throw new \R3T\Exception\R3TValueException("Column $newName already exists in the original columns");
            }
            $index = array_search($oldName, $columns, true);
            $columns[$index] = $newName;
        }
        return $columns;
    }
    public function selectQueryFromPrevious():string{
        $previous = $this->previousOperation->tableConfig->columns;
        $selects = [];
        foreach($previous as $column){
            if(array_key_exists($column, $this->modifications)){
                $selects[] = '"'.$column.'" AS "'.$this->modifications[$column].'"';
            }else{
                $selects[] = '"'.$column.'"';
            }
        }
        return 'SELECT '.implode(',', $selects).' FROM "'.$this->previousOperation->tableConfig->tablename.'"';
    }
    public function transform(iterable $data):iterable{
        foreach($data as $row){
            yield $row;
        }
    }
}