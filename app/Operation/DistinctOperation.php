<?php declare(strict_types=1);
namespace DataTransfer\Operation;
use DataTransfer\Base\OperationInterface;
class DistinctOperation implements OperationInterface{
    public string $query;
    /**
     * distinct column of choise. can only be one column
     */
    public function __construct(string $column){
        
    }
}