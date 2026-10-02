<?php declare(strict_types=1);
namespace DataTransfer\Util;
class DBQueryFormatter{
    public static function prependColon(iterable $data):iterable{
        $returnArray = [];
        foreach($data as $key => $value){
            $returnArray[':'.$key] = $value;
        }
        return $returnArray;
    }
    public static function wrapWithBackticks(iterable|string $data):iterable{
        if(is_string($data)){
            $data = [$data];
        }
        $returnArray = [];
        foreach($data as $column){
            $returnArray[] = '`'.$column.'`';
        }
        return $returnArray;
    }
}