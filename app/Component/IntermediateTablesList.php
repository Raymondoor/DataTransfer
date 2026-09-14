<?php declare(strict_types=1);
namespace DataTransfer\Component;
use DataTransfer\Model\IntermediateTableConfiguration;
class IntermediateTablesList{
    /**
     * @var IntermediateTableConfiguration[]
     */
    public static array $configs = [];
    public static function register(IntermediateTableConfiguration $config):void{
        if(!self::validateName($config)){
            throw new DataTransferException("Intermediate table with name {$config->name} already exists.");
        }
        self::$configs[] = $config;
    }
    public static function validateName(IntermediateTableConfiguration $config):bool{
        foreach(self::$configs as $existingConfig){
            if($existingConfig->name === $config->name){
                return false;
            }
        }
        return true;
    }
}