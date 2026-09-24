<?php declare(strict_types=1);
namespace DataTransfer\Model;

use DataTransfer\Component\IntermediateTableCreator;
/**
 * This manages the intermediate tables in sql, not the configuration itself, so it won't have relations
 */
class IntermediateTableConfiguration{
    /**
     * not set by user, but by the system, to identify the table
     */
    public string $tablename;
    public array $columns;
    public IntermediateTableCreator $creator;
    public function __construct(string $tablename, array $columns){
        $this->tablename = $tablename;
        $this->columns = $columns;
        $this->creator = new IntermediateTableCreator($this);
        $this->creator->parse();
    }
}