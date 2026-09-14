<?php declare(strict_types=1);
namespace Frrame\Util;
class Str{
    public static function length(string $string, ?string $encoding = null):int{
        return mb_strlen($string,$encoding);
    }
    public static function lower(string $string, ?string $encoding = null):string{
        return mb_strtolower($string,$encoding);
    }
    public static function upper(string $string, ?string $encoding = null):string{
        return mb_strtoupper($string,$encoding);
    }
}