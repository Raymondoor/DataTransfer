<?php declare(strict_types=1);
require_once __DIR__.'/../../vendor/autoload.php';
use DataTransfer\DataTransfer;
use DataTransfer\Operation\{AddColumnsOperation, CaptureOperation, DistinctOperation, ExtractOperation, JoinOperation, ModifyValuesOperation, RenameOperation, SettleOperation, UnionOperation};

DataTransfer::boot();
// rename DB first
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
$populateId = $opr::register(ModifyValuesOperation::from($addIdToGroup)->modify(function(iterable $records){ // callback
	$i = 1;
	foreach($records as $record){
		$record['group_id'] = $i;
		$i++;
		yield $record;
		// return structure must not change. only values inside
	}
}));

$joinToSyncGroupId = $opr::register(JoinOperation::from($usersOriginal)->join($populateId)->on('group_name')->source('group')->direction('left'));
$usersFinal = $opr::register(ExtractOperation::from($joinToSyncGroupId)->extract(['id', 'name', 'groups_id', 'created_at'])); // re-extract
$groupsAdjust = $opr::register(RenameOperation::from($populateId)->rename(['groups_id'=>'id','group_name'=>'name'])); // change col name on groups' id
$groupsFinal = $opr::register(AddColumnsOperation::from($groupsAdjust)->add(['created_at'])); // add created_at to groups

$settleGroup = $opr::register(SettleOperation::from($groupsFinal)->settle('groups')); // table and column name must match target
$settleUser = $opr::register(SettleOperation::from($usersFinal)->settle('users')); // settle after groups to respect fereign key constraints

var_dump(DataTransfer::analyze()); // array of operations' query
// DataTransfer::createTables(true); // creates intermediate tables. does not touch actual data yet.
// DataTransfer::transfer(); // execute the transfer apart from settling to new DB
// DataTransfer::settle(); // finalize the transfer and insert to the new DB.