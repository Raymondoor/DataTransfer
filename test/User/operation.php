<?php declare(strict_types=1);
require_once __DIR__.'/../../vendor/autoload.php';
use DataTransfer\Definition\DefinitionInterface;
class ExtractGroupName implements DefinitionInterface{
    public function definition():void{
        ProccessFactory::from()->extract('group_name');
    }
}
