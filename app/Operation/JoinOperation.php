<?php declare(strict_types=1);
namespace R3T\Operation;

use R3T\Exception\R3TException;
class JoinOperation extends Operation{
    public Operation $jointOperation;
    public string $jointColumn;
    public string $sourceColumn;
    public string $direction = 'left';
    public function join(Operation $jointOperation):self{
        $this->jointOperation = $jointOperation;
        return $this;
    }
    public static function from(Operation $previousOperation):self{
        $o = new self();
        $o->id = OperationManager::generateId();
        $o->previousOperation = $previousOperation;
        return $o;
    }
    public function source(string $column):self{
        $this->sourceColumn = $column;
        return $this;
    }
    public function on(string $column):self{
        $this->jointColumn = $column;
        return $this;
    }
    /**
     * Joining direction
     * @param 'left'|'inner'|'right'|'full'|'self' $direction
     */
    public function direction(string $direction = 'LEFT'):self{
        // make sure direction is valid
        $validDirections = ['LEFT', 'INNER', 'RIGHT', 'FULL', 'SELF'];
        if(!in_array(strtoupper($direction), $validDirections, true)){
            throw new R3TException("Invalid join direction '$direction'. Valid directions are: ".implode(', ', $validDirections));
        }
        $this->direction = strtoupper($direction);
        return $this;
    }
    public function validateThenGenerateColumns():array{
        if(!$this->previousOperation::columnExists($this->previousOperation->tableConfig->columns, [$this->sourceColumn])){
            throw new \R3T\Exception\R3TValueException("Join source column does not exist");
        }
        if(!$this->jointOperation::columnExists($this->jointOperation->tableConfig->columns, [$this->jointColumn])){
            throw new \R3T\Exception\R3TValueException("Join target column does not exist");
        }
        return array_merge($this->previousOperation->tableConfig->columns, $this->jointOperation->tableConfig->columns);
    }
    public function selectQueryFromPrevious():string{
        return 'SELECT * FROM `'.$this->previousOperation->tableConfig->tablename.'` '.$this->direction.' JOIN `'.$this->jointOperation->tableConfig->tablename.'` ON `'.$this->previousOperation->tableConfig->tablename.'`.`'.$this->sourceColumn.'` = `'.$this->jointOperation->tableConfig->tablename.'`.`'.$this->jointColumn.'`';
    }
    public function transform(iterable $data):iterable{
        foreach($data as $row){
            yield $row;
        }
    }
}