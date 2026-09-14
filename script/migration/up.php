<?php declare(strict_types=1);
require_once __DIR__.'/../../vendor/autoload.php';
use Frrame\Component\DBstatement;
use Frrame\Migration\CreateLogsTable;
$schemas = [
    CreateLogsTable::class
];
foreach($schemas as $schema){
    DBstatement::exec($schema::up());
}