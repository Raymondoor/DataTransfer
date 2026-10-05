<?php declare(strict_types=1);
namespace DataTransfer\Operation;

use DataTransfer\Database\SrcDB;
/**
 * Magic operation to treat source DB as a previous operation. This operation will replicate the source DB table to a new stage ensuring the modification does not happen in source.
 */
class CaptureOperation extends Operation{
    public bool $temporaryConfiguration = false;
    public string $table;
    public array $tableColumns;
    public function capture(string $table):self{
        $this->table = $table;
        return $this;
    }
    /**
     * Cannot be used if is a temporary configuration
     * @param string $tablename
     * @return CaptureOperation
     */
    public static function fromTable(string $tablename):self{
        $o = new self();
        $o->id = OperationManager::generateId();
        $o->capture($tablename);
        $o->tableColumns = SrcDB::selectColumns($o->table);
        return $o;
    }
    public static function fromColumns(string $tablename, array $columns):self{
        $o = new self();
        $o->id = OperationManager::generateId();
        $o->capture($tablename);
        $o->tableColumns = $columns;
        $o->temporaryConfiguration = true;
        return $o;
    }
    public function validateThenGenerateColumns():array{
        if(!$this->temporaryConfiguration){
            try{
                SrcDB::select('SELECT count(*) FROM `'.$this->table.'` LIMIT 1');
            }catch(\Exception $e){
                throw new \DataTransfer\Exception\DataTransferValueException("Cannot capture from source table, table does not exist.");
            }
        }
        return $this->tableColumns;
    }
    public function selectQueryFromPrevious():string{
        return 'SELECT * FROM `'.$this->table.'`';
    }
    public function transform(iterable $data):iterable{
        foreach($data as $row){
            yield $row;
        }
    }
}