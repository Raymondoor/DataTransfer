<?php declare(strict_types=1);
require_once __DIR__.'/../../vendor/autoload.php';
use DataTransfer\DataTransfer;
use DataTransfer\Operation\ExtractOperation;
use DataTransfer\Operation\JoinOperation;
use DataTransfer\Operation\RenameOperation;
use DataTransfer\Operation\CaptureOperation;
use DataTransfer\Operation\SettleOperation;

DataTransfer::boot();
DataTransfer::setSrcDB('sqlite', __DIR__.'/databaseS.db');
DataTransfer::setOperationalDB('sqlite', __DIR__.'/databaseO.db');
DataTransfer::setTargetDB('sqlite', __DIR__.'/databaseT.db');
DataTransfer::connectDBs();
$opr = DataTransfer::operator(); // returns OperationManager
$opr0 = $opr::register(CaptureOperation::fromTable('users'));
$opr1 = $opr::register(ExtractOperation::from($opr0)->extract(['id','name']));
$opr2 = $opr::register(RenameOperation::from($opr0)->rename(['name' => 'username']));
// $opr3 = $opr::register(JoinOperation::from($opr1)->join($opr2)->source('column1')->on('column2')->direction('left'));

// $opr4 = $opr::register(SettleOperation::from($opr3)->settle('new_table'));

DataTransfer::test();
DataTransfer::createTables();
