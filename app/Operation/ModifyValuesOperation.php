<?php declare(strict_types=1);
namespace R3T\Operation;
/**
 * Modify values of a table by iterating each rows.
 * If a complex or contextual modification is needed, you can join other processes using `JoinOperation` and still won't break the system, since all operations are immutable.
 */
class ModifyValuesOperation extends Operation{
    public ?\Closure $modification = null;
    public string $jointColumn;
    public string $direction = 'left';
    /**
     * Register a modification to the entire record set.
     * @param Closure(iterable):iterable|\Generator $modification
     */
    public function modify(\Closure $modification):self{
        $this->modification = $modification;
        return $this;
    }
    public static function from(Operation $previousOperation):self{
        $o = new self();
        $o->id = OperationManager::generateId();
        $o->previousOperation = $previousOperation;
        return $o;
    }
    public function validateThenGenerateColumns():array{
        return $this->previousOperation->tableConfig->columns;
    }
    public function transform(iterable $data):iterable{
        $result = ($this->modification)($data);
        foreach($result as $row){
            yield $row;
        }
    }
}