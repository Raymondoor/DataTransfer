<?php declare(strict_types=1);
namespace DataTransfer\Operation;
use DataTransfer\Base\OperationInterface;
class Unionperation implements OperationInterface{
    public string $query;
    /**
     * list of joining table.columns
     * @param string[] $columns
     * @return self
     */
    public function source(array $columns):self{
        return $this;
    }
    public function on(string $column):self{
        return $this;
    }
    /**
     * Joining direction
     * @param 'left'|'both'|'right' $direction
     * @return void
     */
    public function direction(string $direction):self{
        return $this;
    }
}