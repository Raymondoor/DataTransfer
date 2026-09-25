<?php declare(strict_types=1);
namespace DataTransfer\Operation;
use DataTransfer\Model\IntermediateTableConfiguration;
abstract class Operation{
    public string $id;
    public ?Operation $previousOperation = null;
    public IntermediateTableConfiguration $tableConfig;
    /**
     * Returns the newly generated columns list based on the original columns
     * @throws \DataTransfer\Exception\DataTransferException;
     */
    public function setTableConfiguration(string $tablename, array $columns):void{
        $this->tableConfig = new IntermediateTableConfiguration($tablename, $columns);
    }
    abstract public function validateThenGenerateColumns():?array;
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
    public function selectQueryFromPrevious(Operation $previous):string{
        return 'SELECT * FROM `'.$previous->tableConfig->tablename.'`';
    }
    /**
     * Result data after transformation. It has to match the format
     * @param iterable $data Data selected from Operation::selectQueryFromPrevious() query is yielded here.
     * @return array<string<array>> Generator, or it can be an array if needed.
     */
    abstract public function transform(iterable $data):iterable;
    // abstract public function 
    public function getOperationName():string{
        return (new \ReflectionClass($this))->getShortName();
    }
}