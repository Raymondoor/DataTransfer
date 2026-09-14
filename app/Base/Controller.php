<?php declare(strict_types= 1);
namespace DataTransfer\Base;
/**
 * Blueprint controllers extend.
 */
abstract class Controller{
    /**
     * Entry point a route dispatches to.
     */
    abstract public static function index():void;
}