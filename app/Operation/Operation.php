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
    abstract public function validateThenGenerateColumns():array;
    public function setTableConfiguration(string $tablename, array $columns):void{
        $this->tableConfig = new IntermediateTableConfiguration($tablename, $columns);
    }
    // abstract public function 
    public function getOperationName():string{
        return (new \ReflectionClass($this))->getShortName();
    }
    public static function columnExists(array $needle, array $haystack):string|false{
        foreach($haystack as $column){
            if(in_array($column, $needle,false)){
                return $column;
            }
        }
        return false;
    }
}