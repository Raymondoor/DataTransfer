<?php declare(strict_types=1);
namespace DataTransfer\Operation;
use DataTransfer\Database\TrgtDB;
use DataTransfer\Table\IntermediateTableConfiguration;
use DataTransfer\Exception\DataTransferException;
/**
 * Magic operation to treat target DB insert as a settlement operation. This operation will not generate any new columns, but will validate the existing columns and ensure that the data is settled correctly.
 */
class SettleOperation extends Operation{
    public string $table;
    public array $tableColumns;
    /**
     * @var string[]
     * @todo implement type conversions at the last moment?
     */
    public array $dataTypes = [];
    /**
     * Creates a new table configuration and sets to `$this->tableConfig`
     */
    public function setTableConfiguration():void{
        $this->tableConfig = new IntermediateTableConfiguration($this->table, $this->validateThenGenerateColumns());
        $this->tableConfig->setInsert();
    }
    public function settle(string $table):self{
        $this->table = $table;
        return $this;
    }
    public static function from(Operation $previousOperation):self{
        $o = new self();
        $o->id = OperationManager::generateId();
        $o->previousOperation = $previousOperation;
        return $o;
    }
    public function setColumnsFromTable():void{
        $this->tableColumns = TrgtDB::selectColumns($this->table);
    }
    public function validateThenGenerateColumns():array{
        $columns = $this->previousOperation->tableConfig->columns;
        $this->setColumnsFromTable();
        $sourceColumns = $columns;
        $targetColumns = $this->tableColumns;
        sort($sourceColumns, SORT_STRING);
        sort($targetColumns, SORT_STRING);
        if($sourceColumns !== $targetColumns){
            throw new DataTransferException("Cannot settle to target table, column names seems to not match.");
        }
        return $this->tableColumns;
    }
    public function transform(iterable $data):iterable{
        foreach($data as $row){
            yield $row;
        }
    }
}