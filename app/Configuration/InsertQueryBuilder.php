<?php declare(strict_types=1);
namespace DataTransfer\Configuration;
use DataTransfer\Configuration\TableConfiguration;
use DataTransfer\Exception\DataTransferException;
use DataTransfer\Database\OprDB;
class InsertQueryBuilder{
    public string $query;
    public function __construct(public TableConfiguration $config)
    {
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