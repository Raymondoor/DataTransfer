<?php declare(strict_types=1);
namespace DataTransfer\Operation;

use DataTransfer\Exception\DataTransferException;
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
     * @param 'left'|'both'|'right' $direction
     * @return void
     */
    public function direction(string $direction = 'left'):self{
        $this->direction = $direction;
        return $this;
    }
    public function validateThenGenerateColumns():array{
        // @todo implement
        // select cols from previous and joint operation, find join, etc
        if(!$this->previousOperation::columnExists($this->previousOperation->tableConfig->columns, [$this->sourceColumn])){
            throw new DataTransferException("Join source column does not exist");
        }
        if(!$this->jointOperation::columnExists($this->jointOperation->tableConfig->columns, [$this->jointColumn])){
            throw new DataTransferException("Join target column does not exist");
        }
        // generate new table columns by merging previous and joint operation columns
        $columns = array_merge($this->previousOperation->tableConfig->columns, $this->jointOperation->tableConfig->columns);
        return $columns;
    }
    public function selectQueryFromPrevious():string{
        // @todo implement
        return 'SELECT * FROM '.$this->previousOperation->tableConfig->tablename.' JOIN '.$this->jointOperation->tableConfig->tablename.' ON '.$this->previousOperation->tableConfig->tablename.'.'.$this->sourceColumn.' = '.$this->jointOperation->tableConfig->tablename.'.'.$this->jointColumn;
    }
    public function transform(iterable $data):iterable{
        // @todo implement
        // just select and send back directly. new cols should be null anyways
        yield [];
    }
}