<?php declare(strict_types=1);
namespace DataTransfer\Operation;
use DataTransfer\Table\IntermediateTableConfiguration;
abstract class Operation{
    public string $id;
    public ?Operation $previousOperation = null;
    public IntermediateTableConfiguration $tableConfig;
    /**
     * Creates a new table configuration and sets to `$this->tableConfig`
     */
    public function setTableConfiguration():void{
        $this->tableConfig = new IntermediateTableConfiguration($this->id, $this->validateThenGenerateColumns());
        $this->tableConfig->setCreate();
        $this->tableConfig->setInsert();
    }
    /**
     * As it says, validates if the relation is correct or not based on the original columns, then returns the newly generated columns list
     * @return ?array
     * @throws \DataTransfer\Exception\DataTransferException;
     */
    abstract public function validateThenGenerateColumns():?array;
    /**
     * Checks if in a given array, for each column, there is a match in the needle.
     * @return string|false returns the first match or false if there isn't.
     */
    public static function columnExists(array $needle, array $haystack):string|false{
        foreach($haystack as $column){
            if(in_array($column, $needle,false)){
                return $column;
            }
        }
        return false;
    }
    /**
     * Query to be used for selecting from the previous data set. Customize if needed.
     * This is part of the transformation operation already. Some data are better to be handled in database, some are better handled in logic.
     * Having said that, it is better to avoid customizing the query, since it may cause some complications
     * @return string the query string to select data.
     */
    public function selectQueryFromPrevious():string{
        return 'SELECT * FROM `'.$this->previousOperation->tableConfig->tablename.'`';
    }
    /**
     * Result data after transformation. It has to match the format
     * @param iterable $data Data selected from `Operation::selectQueryFromPrevious()` query is yielded here.
     * @return array<string<array>> Generator, or it can be an array if needed.
     */
    abstract public function transform(iterable $data):iterable;
    // abstract public function 
    public function getOperationName():string{
        return (new \ReflectionClass($this))->getShortName();
    }
}