<?php declare(strict_types=1);
namespace DataTransfer\Table;
use DataTransfer\Table\IntermediateTableConfiguration;
use DataTransfer\Exception\DataTransferException;
use DataTransfer\Database\OprDB;
class IntermediateTableInserter{
    public string $query;
    public IntermediateTableConfiguration $config;
    public function __construct(IntermediateTableConfiguration $config){
        $this->config = $config;
    }
    public function createQuery():void{
        $this->query = "INSERT INTO `".$this->config->tablename."` (";
        foreach($this->config->columns as $column){
            $this->query .= "`".$column."`,";
        }
        $this->query = rtrim($this->query,",");
        $this->query .= ") VALUES (";
        foreach($this->config->columns as $column){
            $this->query .= ":".$column.",";
        }
        $this->query = rtrim($this->query,",");
        $this->query .= ")";
    }
}