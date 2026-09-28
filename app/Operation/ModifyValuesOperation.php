<?php declare(strict_types=1);
namespace DataTransfer\Operation;
/**
 * Modify values of a table by iterating each rows.
 * If a complex or contextual modification is needed, you can join other processes using `JoinOperation` and still won't break the system, since all operations are immutable.
 */
class ModifyValuesOperation extends Operation{
    public ?\Closure $modification;
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
    public function validateThenGenerateColumns():array{
        $columns = $this->previousOperation->tableConfig->columns;
        return $columns;
    }
    public function transform(iterable $data):iterable{
        yield ($this->modification)($data);
    }
}