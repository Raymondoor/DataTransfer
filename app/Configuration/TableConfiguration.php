<?php declare(strict_types=1);
namespace DataTransfer\Configuration;
/**
 * This manages the intermediate tables in sql, not the configuration itself, so it won't have relations
 */
class TableConfiguration{
    /**
     * not set by user, but by the system, to identify the table
     */
    public string $tablename;
    public array $columns;
    public CreateTableQueryBuilder $creator;
    public InsertQueryBuilder $inserter;
    public function __construct(string $tablename, array $columns){
        $this->tablename = $tablename;
        $this->columns = $columns;
    }
    public function setCreate():void{
        $this->creator = new CreateTableQueryBuilder($this);
        $this->creator->createQuery();
    }
    public function setInsert():void{
        $this->inserter = new InsertQueryBuilder($this);
        $this->inserter->createQuery();
    }
}