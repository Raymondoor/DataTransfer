# DataTransfer
A PHP data migration tool.

## Overview
A PHP library for describing and preparing database migrations as a sequence of data operations. It separates the source, an operational staging database, and the destination so that migration work can be organized and inspected before it is applied.

## Installation
```sh
composer require raymondoor/datatransfer
```

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

## Why DataTransfer?

### Make complicated migrations manageable

Database migrations can become difficult when the destination is not simply a newer version of the source.

A schema may need to be split apart, combined, reorganized, or have its data represented differently. What starts as a few schema changes can quickly become a complicated chain of data transformations.

DataTransfer takes a different approach:

> **Break the complicated migration into simple transformations.**

Rather than trying to express the entire migration as one large operation, each transformation can be kept small and composed with the others.

A complicated migration can therefore be built progressively:

```text
source
  ↓
simple transformation
  ↓
simple transformation
  ↓
simple transformation
  ↓
target
```

The complexity is still there — but it is organized into steps that can be understood individually.

This makes it possible to handle substantial changes in how data is structured without turning the migration itself into an increasingly complicated piece of code.

### Keep migrations understandable

Complexity is only half of the problem.

A migration that works today can still become difficult to understand, inspect, or change later.

DataTransfer is designed around **immutable operations**. A transformation does not silently alter the result of an earlier transformation. Instead, each operation produces a new result that can become the basis for subsequent work.

That gives the migration a history.

Each step can be examined independently, and the relationships between steps can be analyzed rather than hidden inside a sequence of destructive changes.

The migration can therefore be treated not just as something to execute, but as something that can be **understood, analyzed, and recorded**.

When a migration changes, you can reason about what changed and where it affects the process.

> **A migration should be more than something that runs. It should be something you can understand.**

## Docs
Not written yet...