<?php declare(strict_types=1);
namespace DataTransfer\Component;
use DataTransfer\Model\IntermediateStageConfiguration;
class IntermediateStagesManager{
    /**
     * @var IntermediateStageConfiguration[]
     */
    public static array $configs = [];
    public static function register(IntermediateStageConfiguration $config):void{
        if(!self::validateName($config)){
            throw new DataTransferException("Intermediate Stage with name {$config->name} already exists.");
        }
        self::$configs[] = $config;
    }
    public static function validateName(IntermediateStageConfiguration $config):bool{
        foreach(self::$configs as $existingConfig){
            if($existingConfig->name === $config->name){
                return false;
            }
        }
        return true;
    }
}