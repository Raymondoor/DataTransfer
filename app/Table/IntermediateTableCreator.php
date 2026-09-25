<?php declare(strict_types=1);
namespace DataTransfer\Table;
use DataTransfer\Table\IntermediateTableConfiguration;
use DataTransfer\Exception\DataTransferException;
use DataTransfer\Database\OprDB;
class IntermediateTableCreator{
    public string $query;
    public IntermediateTableConfiguration $config;
    public function __construct(IntermediateTableConfiguration $config){
        $this->config = $config;
        $this->query = "CREATE TABLE IF NOT EXISTS `".$config->tablename."` (";
    }
    public function create():bool{
        if(OprDB::exec($this->query) === false){
            throw new DataTransferException("Failed to create intermediate table: ".$this->config->tablename);
        }
        return true;
    }
    public function parse():void{
        foreach($this->config->columns as $column){
            $this->query .= "`".$column."`,";
        }
        $this->query = rtrim($this->query,",");
        $this->query .= ")";
    }
}