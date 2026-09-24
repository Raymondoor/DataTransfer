<?php declare(strict_types=1);
namespace DataTransfer;

use DataTransfer\Database\SrcDB;
use DataTransfer\Database\OprDB;
use DataTransfer\Database\TrgtDB;
use DataTransfer\Exception\DataTransferException;
use DataTransfer\Operation\OperationManager;
class DataTransfer{
    public static bool $isSrcDBSet = false;
    public static bool $isOprDBSet = false;
    public static bool $isTrgtDBSet = false;
    public static function boot(array $config = []):void{
        
    }
    public static function setSrcDB(string $driver, string $host, string $name = '', string $user = '', string $pass = '', array $options = []):void{
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
    public static function test():bool{
        foreach(OperationManager::$operationList as $operation){
            var_dump($operation->tableConfig->creator->query);
        }
        return true;
    }
    public static function createTables():bool{
        return true;
    }
    public static function execute():bool{
        return true;
    }
}