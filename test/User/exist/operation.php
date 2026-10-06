<?php declare(strict_types=1);
require_once __DIR__.'/../../../vendor/autoload.php';
use DataTransfer\DataTransfer;
use DataTransfer\Operation\{AddColumnsOperation, CaptureOperation, DistinctOperation, ExtractOperation, JoinOperation, ModifyValuesOperation, RenameOperation, SettleOperation, UnionOperation};

DataTransfer::boot();
DataTransfer::setSourceDB('sqlite', __DIR__.'/databaseS.db');
DataTransfer::setOperationalDB('sqlite', __DIR__.'/databaseO.db');
DataTransfer::setTargetDB('sqlite', __DIR__.'/databaseT.db');
DataTransfer::connectDBs();
$opr = DataTransfer::operator();
$usersOriginal = $opr::register(CaptureOperation::fromTable('users'));
$groupsExtracted = $opr::register(ExtractOperation::from($usersOriginal)->extract('group'));
$distinctGroups = $opr::register(DistinctOperation::from($groupsExtracted)->distinct('group'));
$renameAdjustGroups = $opr::register(RenameOperation::from($distinctGroups)->rename(['group' => 'group_name']));
$addIdToGroup = $opr::register(AddColumnsOperation::from($renameAdjustGroups)->add(['groups_id']));
$populateId = $opr::register(ModifyValuesOperation::from($addIdToGroup)->modify(function(iterable $records){
	$i = 1;
	foreach($records as $record){
		$record['groups_id'] = $i;
		$i++;
		yield $record;
	}
}));
$joinToSyncGroupId = $opr::register(JoinOperation::from($usersOriginal)->join($populateId)->on('group_name')->source('group')->direction('left'));
$usersFinal = $opr::register(ExtractOperation::from($joinToSyncGroupId)->extract(['id', 'name', 'groups_id', 'created_at']));
$groupsAdjust = $opr::register(RenameOperation::from($populateId)->rename(['groups_id'=>'id','group_name'=>'name']));
$groupsAddTimestamp = $opr::register(AddColumnsOperation::from($groupsAdjust)->add(['created_at']));
$groupsFinal = $opr::register(ModifyValuesOperation::from($groupsAddTimestamp)->modify(function(iterable $records){
	foreach($records as $record){
		$record['created_at'] = date('Y-m-d H:i:s');
		yield $record;
	}
}));
$settleGroup = $opr::register(SettleOperation::from($groupsFinal)->settle('groups'));
$settleUser = $opr::register(SettleOperation::from($usersFinal)->settle('users'));

dump(DataTransfer::analyze());
DataTransfer::transfer();
// DataTransfer::settle();