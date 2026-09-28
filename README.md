# DataTransfer
A PHP data migration tool.

## Overview
A PHP library for describing and preparing database migrations as a sequence of data operations. It separates the source, an operational staging database, and the destination so that migration work can be organized and inspected before it is applied.


## Usage
The example illustrates the intended migration flow.

```php
use DataTransfer\DataTransfer;
use DataTransfer\Operation\{AddColumnsOperation, CaptureOperation, DistinctOperation, ExtractOperation, JoinOperation, ModifyValuesOperation, RenameOperation, SettleOperation, UnionOperation};

DataTransfer::boot();
DataTransfer::setSourceDB('sqlite', '/path/to/db.sqlite'); // original
DataTransfer::setOperationalDB('sqlite', '/path/to/temp/db.sqlite'); // transactional DB
DataTransfer::setTargetDB('mysql','host','dbname','user','pass'); // target database
DataTransfer::connectDBs(); // establish PDO connection

$opr = DataTransfer::operator(); // returns OperationManager
$usersOriginal = $opr::register(CaptureOperation::fromTable('users')); // eg. consists of [id, name, password, group]

$groupsExtracted = $opr::register(ExtractOperation::from($usersOriginal)->extract('group'));
$distinctGroups = $opr::register(DistinctOperation::from($groupsExtracted)->distinct('group'));
$renameAdjustGroups = $opr::register(RenameOperation::from($distinctGroups)->rename(['group' => 'group_name']));
$addIdToGroup = $opr::register(AddColumnsOperation::from($renameAdjustGroups)->add('groups_id'));
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
$usersFinal = $opr::register(ExtractOperation::from($joinToSyncGroupId)->extract(['id','name', 'password', 'groups_id'])); // re-extract
$groupsFinal = $opr::register(RenameOperation::from($populateId)->rename(['groups_id'])->to(['id'])); // change col name on groups' id

$settleGroup = $opr::register(SettleOperation::from($groupsFinal)->settle('groups')); // table and column name must match target
$settleUser = $opr::register(SettleOperation::from($usersFinal)->settle('users')); // settle after groups to respect fereign key constraints

var_dump(DataTransfer::analyze()); // array of operations' query
DataTransfer::createTables(true); // creates intermediate tables. does not touch actual data yet.
DataTransfer::transfer(); // execute the transfer apart from settling to new DB
DataTransfer::settle(); // finalize the transfer and insert to the new DB.
```