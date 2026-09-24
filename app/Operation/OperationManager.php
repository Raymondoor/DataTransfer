<?php declare(strict_types=1);
namespace DataTransfer\Operation;
class OperationManager{
    /**
     * @var Operation[]
     */
    public static array $operationList = [];
    public static int $idCounter = 0;
    public static function boot():self{
        return new static();
    }
    public static function generateId():string{
        // internal counter is int but generate with string zero padding for better readability
        return str_pad((string)self::incrementIdCounter(), 4, '0', STR_PAD_LEFT);
    }
    private static function incrementIdCounter():int{
        return ++self::$idCounter;
    }
    /**
     * Intercept and register the operation to list and return directly.
     */
    public static function register(Operation $operation):Operation{
        self::$operationList[] = $operation;
        return $operation;
    }
}