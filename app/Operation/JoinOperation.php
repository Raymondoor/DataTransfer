<?php declare(strict_types=1);
namespace DataTransfer\Operation;
use DataTransfer\Base\OperationInterface;
class JoinOperation implements OperationInterface{
    /**
     * list of joining table.columns
     * @param string $column
     * @return self
     */
    public function source(string $column):self{
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