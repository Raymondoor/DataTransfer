<?php declare(strict_types=1);
namespace DataTransfer\Util;
class DBParamFormatter{
    public static function prependColon(iterable $data):iterable{
        $returnArray = [];
        foreach($data as $key => $value){
            $returnArray[':'.$key] = $value;
        }
        return $returnArray;
    }
}