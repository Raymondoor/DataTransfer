<?php declare(strict_types=1);
namespace R3T\Configuration;
use R3T\Configuration\TableConfiguration;
use R3T\Exception\R3TException;
use R3T\Database\OprDB;
use R3T\Util\DBQueryFormatter;
class InsertQueryBuilder{
    public string $query;
    public function __construct(
        public TableConfiguration $config
    ){}
    public function createQuery():void{
        $enc = DBQueryFormatter::getEncapsulation($this->config->driver);
        $this->query = 'INSERT INTO '.$enc.$this->config->tablename.$enc.' (';
        foreach($this->config->columns as $column){
            $this->query .= $enc.$column.$enc.',';
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