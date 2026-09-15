<?php declare(strict_types=1);
namespace DataTransfer\Operation;
use DataTransfer\Base\OperationInterface;
class AddColumnOperation implements OperationInterface{
    public string $query;
    /**
     * column to add
     */
    public function add(string $column):self{
        return $this;
    }
}