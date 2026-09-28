<?php declare(strict_types=1);
namespace DataTransfer\Operation;

use DataTransfer\Exception\DataTransferException;
class UnionOperation extends Operation{
    public Operation $unionOperation;
    public function union(Operation $unionOperation):self{
        $this->unionOperation = $unionOperation;
        return $this;
    }
    public static function from(Operation $previousOperation):self{
        $o = new self();
        $o->id = OperationManager::generateId();
        $o->previousOperation = $previousOperation;
        return $o;
    }
    public function validateThenGenerateColumns():array{
        $diffOnPrevious = array_diff($this->previousOperation->tableConfig->columns,$this->unionOperation->tableConfig->columns);
        if($diffOnPrevious !== []){
            throw new DataTransferException('Columns: '.implode(', ',$diffOnPrevious).'does not exist on the previous operation');
        }
        $diffOnUnion = array_diff($this->unionOperation->tableConfig->columns,$this->previousOperation->tableConfig->columns);
        if($diffOnUnion !== []){
            throw new DataTransferException('Columns: '.implode(', ',$diffOnUnion).'does not exist on the union operation');
        }
        return $this->previousOperation->tableConfig->columns;
    }
    public function selectQueryFromPrevious():string{
        // @todo implement union
        return 'SELECT * FROM '.$this->previousOperation->tableConfig->tablename;
    }
    public function transform(iterable $data):iterable{
        foreach($data as $row){
            yield $row;
        }
    }
}