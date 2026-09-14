<?php declare(strict_types=1);
namespace DataTransfer\Model;
class IntermediateTableConfiguration{
    public string $name;
    public array $columns;
    public ?array $from = null;
    public ?array $to = null;
    public function __construct(string $name, array $columns){
        $this->name = $name;
        $this->columns = $columns;
    }
    /**
     * @param IntermediateTableConfiguration[] $from
     * @return void
     */
    public function setFrom(array $from):void{
        $this->from = $from;
    }
    /**
     * @param IntermediateTableConfiguration[] $to
     * @return void
     */
    public function setTo(array $to):void{
        $this->to = $to;
    }
}