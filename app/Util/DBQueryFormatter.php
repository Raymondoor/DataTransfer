<?php declare(strict_types=1);
namespace R3T\Util;
class DBQueryFormatter{
    /**
     * Adds a `:` in front of the key of parameters for a `:foo` style prepared statement.
     * ['foo' => 'bar'] becomes [':foo' => 'bar']
     */
    public static function prependColon(iterable $data):iterable{
        $returnArray = [];
        foreach($data as $key => $value){
            $returnArray[':'.$key] = $value;
        }
        return $returnArray;
    }
    /**
     * Wraps each element in an array with backticks.
     * ['foo'] becomes ['\`foo\`']
     */
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