<?php declare(strict_types=1);
namespace DataTransfer\Component;
use DataTransfer\Model\IntermediateTableConfiguration;
class IntermediateTableCreator{
    public string $query;
    public IntermediateTableConfiguration $config;
    public function __construct(IntermediateTableConfiguration $config){
        $this->config = $config;
        $this->query = "CREATE TABLE IF NOT EXISTS `".$config->name."` (";
    }
    public function create():bool{
        if(OprDB::exec($this->query) === 1){
            return true;
        }
        throw new DataTransferException("Failed to create intermediate table: ".$this->config->name);
    }
    public function parse():void{
        foreach($this->config->columns as $column){
            $this->query .= "`".$column->name."` ".$column->type;
            $this->query .= ",";
        }
        $this->query = rtrim($this->query,",");
        $this->query .= ")";
    }
}