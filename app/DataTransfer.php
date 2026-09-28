<?php declare(strict_types=1);
namespace DataTransfer;

use DataTransfer\Database\SrcDB;
use DataTransfer\Database\OprDB;
use DataTransfer\Database\TrgtDB;
use DataTransfer\Exception\DataTransferException;
use DataTransfer\Operation\CaptureOperation;
use DataTransfer\Operation\OperationManager;
use DataTransfer\Operation\SettleOperation;
use DataTransfer\Util\DBParamFormatter;
class DataTransfer{
    public static array $config = [
        'cli' => true,
    ];
    public static bool $isSrcDBSet = false;
    public static bool $isOprDBSet = false;
    public static bool $isTrgtDBSet = false;
    public static function boot(array $config = []):void{
        self::$config = array_merge(self::$config, $config);
    }
    public static function setSourceDB(string $driver, string $host, string $name = '', string $user = '', string $pass = '', array $options = []):void{
        SrcDB::$driver = $driver;
        SrcDB::$host = $host;
        SrcDB::$name = $name;
        SrcDB::$user = $user;
        SrcDB::$pass = $pass;
        SrcDB::$options = array_merge(SrcDB::$options, $options);
        self::$isSrcDBSet = true;
    }
    public static function setOperationalDB(string $driver, string $host, string $name = '', string $user = '', string $pass = '', array $options = []):void{
        OprDB::$driver = $driver;
        OprDB::$host = $host;
        OprDB::$name = $name;
        OprDB::$user = $user;
        OprDB::$pass = $pass;
        OprDB::$options = array_merge(OprDB::$options, $options);
        self::$isOprDBSet = true;
    }
    public static function setTargetDB(string $driver, string $host, string $name = '', string $user = '', string $pass = '', array $options = []):void{
        TrgtDB::$driver = $driver;
        TrgtDB::$host = $host;
        TrgtDB::$name = $name;
        TrgtDB::$user = $user;
        TrgtDB::$pass = $pass;
        TrgtDB::$options = array_merge(TrgtDB::$options, $options);
        self::$isTrgtDBSet = true;
    }
    public static function connectDBs():void{
        if(!self::$isSrcDBSet){
            throw new DataTransferException("Source DB is not set.");
        }
        if(!self::$isOprDBSet){
            throw new DataTransferException("Operational DB is not set.");
        }
        if(!self::$isTrgtDBSet){
            throw new DataTransferException("Target DB is not set.");
        }
        SrcDB::connect();
        OprDB::connect();
        TrgtDB::connect();
    }
    public static function operator():OperationManager{
        return OperationManager::boot();
    }
    /**
     * Checks config validity and returns all schema that will be ran.
     * @todo implement warning feat on src & target column if not used.
     * @return ?array
     */
    public static function analyze():?array{
        OperationManager::setAllTableConfiguration();
        $query = [];
        foreach(OperationManager::$operationList as $operation){
            $set = [];
            $reflection = new \ReflectionClass($operation::class);
            $set['operation'] = $reflection->getShortName();
            $set['id'] = $operation->id;
            if($operation instanceof SettleOperation){
                $set['create'] = null;
                // var_dump('No new schema on operation: '.$operation->id);
            }else{
                $set['create'] = $operation->tableConfig->creator->query;
            }
            $set['select'] = $operation->selectQueryFromPrevious();
            $set['insert'] = $operation->tableConfig->inserter->query;
            $query[] = $set;
        }
        return $query;
    }
    /**
     * Create all intermediate tables registered in operation. Is created in database set in `DataTransfer::setOperationalDB()`.
     * @param bool $reset `true` deletes all existing tables inside operational DB. Useful when re-running many times to test the configuration.
     */
    public static function createTables(bool $reset = false):bool{
        OperationManager::setAllTableConfiguration();
        if($reset)OprDB::dropAllTables();
        foreach(OperationManager::$operationList as $operation){
            if($operation instanceof SettleOperation){
            }else{
                if(OprDB::exec($operation->tableConfig->creator->query) === false){
                    throw new DataTransferException("Failed to create intermediate table: ".$operation->tableConfig->tablename);
                }
            }
        }
        return true;
    }
    /**
     * Execute the transfer apart from settling to new DB
     */
    public static function transfer():bool{
        self::createTables();
        foreach(OperationManager::$operationList as $operation){
            if(self::$config['cli']){
                echo 'Operation: '.$operation->id.' started... ';
            }
            if($operation instanceof SettleOperation){
                if(self::$config['cli']){
                    echo 'skipped for later.'.PHP_EOL;
                }
            }else{
                if($operation instanceof CaptureOperation){
                    $data = SrcDB::selectUnbuffered($operation->selectQueryFromPrevious());
                }else{
                    $data = OprDB::selectUnbuffered($operation->selectQueryFromPrevious());
                }
                $newData = $operation->transform($data);
                foreach($newData as $newRecord){
                    OprDB::run($operation->tableConfig->inserter->query, DBParamFormatter::appendColon($newRecord));
                }
                if(self::$config['cli']){
                    echo 'completed.'.PHP_EOL;
                }
            }
        }
        return true;
    }
    /**
     * Finalize the transfer and insert to the new DB. Cannot run if there is no prior operation/transfer.
     * @todo implement warning if data already exists in target
     */
    public static function settle():bool{
        foreach(OperationManager::$operationList as $operation){
            if($operation instanceof SettleOperation){
                if(self::$config['cli']){
                    echo 'Operation: '.$operation->id.' started... ';
                }
                $data = OprDB::selectUnbuffered($operation->selectQueryFromPrevious());
                $newData = $operation->transform($data);
                foreach($newData as $newRecord){
                    TrgtDB::run($operation->tableConfig->inserter->query, DBParamFormatter::appendColon($newRecord));
                }
                if(self::$config['cli']){
                    echo 'transfer completed.'.PHP_EOL;
                }
            }
            
        }
        return true;
    }
    /**
     * Transfer and settle to new DB
     * @todo not implemented yet
     */
    public static function execute():bool{
        foreach(OperationManager::$operationList as $operation){
            // if instance of capture, use SrcDB, if instance of Settle, use TrgtDB
        }
        return true;
    }
}