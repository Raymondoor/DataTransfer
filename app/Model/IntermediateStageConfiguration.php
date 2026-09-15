<?php declare(strict_types=1);
namespace DataTransfer\Model;

use DataTransfer\Base\OperationInterface;
use DataTransfer\Component\IntermediateTableCreator;
class IntermediateStageConfiguration{
    public string $identity;
    public string $name;
    public array $columns;
    public ?array $from = null;
    public ?array $usedBy = null;
    public OperationInterface $operation;
    public IntermediateTableConfiguration $tableConfig;
    public array $relations;
    public function __construct(string $name, array $columns){
        $this->name = $name;
        $this->columns = $columns;
    }
    /**
     * @param IntermediateStageConfiguration[] $from
     * @return void
     */
    public function setFrom(array $from):void{
        $this->from = $from;
    }
    /**
     * @param IntermediateStageConfiguration[] $usedBy
     * @return void
     */
    public function setUsedBy(array $usedBy):void{
        $this->usedBy = $usedBy;
    }
    public function setOperations(OperationInterface $operation):void{
        $this->operation = $operation;
    }
}