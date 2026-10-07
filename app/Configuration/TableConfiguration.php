<?php declare(strict_types=1);
namespace R3T\Configuration;
/**
 * This manages the intermediate tables in sql, not the configuration itself, so it won't have relations
 */
class TableConfiguration{
    public CreateTableQueryBuilder $creator;
    public InsertQueryBuilder $inserter;
    public function __construct(
        /**
         * not set by user, but by the system, to identify the table
         */
        public string $tablename,
        public array $columns
    )
    {
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