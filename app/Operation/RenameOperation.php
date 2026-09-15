<?php declare(strict_types=1);
namespace DataTransfer\Operation;
use DataTransfer\Base\OperationInterface;
class RenameOperation implements OperationInterface{
    public string $query;
    /**
     * list of columns to rename
     * @param string[] $columns
     * @return self
     */
    public function rename(array $columns):self{
        return $this;
    }
}