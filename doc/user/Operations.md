# Operations

Operations are the core of R3T. It defines how data is transformed as it moves through the trasnfer process. Each operation produces a result that can be used by subsequent operations, allowing a migration to be composed from smaller steps.

## Operation Class

`R3T\Operation\Operation` is the root class for operations. The predefined operations implement different ways of selecting, combining, or modifying data. 

You can also extend the operation classes to implement custom behavior, however that is with its own responsibility.

In order for the transfer to work properly, all operations has to be registered to `R3T\Operation\OperationManager`. This can be called by running the `R3T::operator()` method. In the below example we'll assume it was assigned to `$opr`.

## Magic Operations
Before talking about the "normal" operations (next section), we need to clear two operations reserved for doing something special. 

### CaptureOperation and SettleOperation

These operations connect the transformation chain to the source and target databases.

`CaptureOperation` starts a chain from a source table:

```php
$usersOriginal = $opr::register(
    CaptureOperation::fromTable('users')
);
```

`SettleOperation` marks the result to be inserted into a target table:

```php
$settleUser = $opr::register(
    SettleOperation::from($usersFinal)->settle('users')
);
```

The result's column names must match the destination table's column names. When multiple target tables have dependencies (like foreign key constraints), register their settle operations in the required order.


## Regular Operations
Here is the description on how each operations behave and how you can use them.

### ExtractOperation
Selects columns from a table.

```php
$extracted = $opr::register(
    ExtractOperation::from($originalCapture)->extract(['column1','column3'])
);
```
From a table of
| column1 | column2 | column3 | column4 |
| - | - | - | - |
| record1-1 | record1-2 | record1-3 | record1-4 |
| record2-1 | record2-2 | record2-3 | record2-4 |

produces 
| column1 | column3 |
| - | - |
| record1-1 | record1-3 |
| record2-1 | record2-3 |

### DistinctOperation

Selects distinct values from a specified column.

```php
$distinct = $opr::register(
    DistinctOperation::from($roles)->distinct('role')
);
```

From a table of 
| user | role |
| - | - |
| user1 | admin |
| user2 | user |
| user3 | user |
| user4 | guest |
| user5 | guest |

produces
| role |
| - |
| admin |
| user |
| guest |

### RenameOperation

Renames columns while retaining their values.

```php
$rename = $opr::register(
    RenameOperation::from($groups)
        ->rename(['group' => 'group_name'])
);
```

The column changes from `group` to `group_name`; its values remain unchanged.

This
| group |
| - |
| groupA |
| ... |

becomes
| group_name |
| - |
| groupA |
| ... |

### AddColumnsOperation

Adds columns to the result. The new columns are initially populated with `null`.

```php
$addCols = $opr::register(
    AddColumnsOperation::from($groups)
        ->add(['groups_id'])
);
```

If the input has columns `[group_name]`, the result has columns `[group_name, groups_id]`, with `groups_id` initially `null`.

| group_name |
| - |
| groupA |
| ... |

to
| group_name | groups_id |
| - | - |
| groupA | NULL |
| ... | NULL |

### ModifyValuesOperation

Modifies record values using a callback. The callback must preserve the record structure: it changes values, not the declared columns.

```php
$modifyVals = $opr::register(
    ModifyValuesOperation::from($codes)
        ->modify(function(iterable $records){
            foreach($records as $record){
                if(!empty($record['code1']) && !empty($record['code2'])){
                    // "code" is a newly added column
                    $record['code'] = $record['code1'].$record['code2'];
                }else{
                    $record['code'] = '00000000';
                }
                $record['updated_at'] = date('Y-m-d H:i:s');
                yield $record;
            }
        })
);
```

For input records with `group_name` values `Admin` and `User`, the output has columns `[group_name, groups_id]` and values:

| code1 | code2 | code | updated_at |
| - | - | - | - |
| 0001 | AAAA | NULL | NULL |
| 0002 | NULL | NULL | NULL |

becomes
| code1 | code2 | code | updated_at |
| - | - | - | - |
| 0001 | AAAA | 0000AAAA | 2000-01-01 00:00:01 |
| 0002 | NULL | 00000000 | 2000-01-01 00:00:01 |

### JoinOperation

Combines two operation results using a matching column and a specified join direction.

```php
$joinToSync = $opr::register(
    JoinOperation::from($users)
        ->join($groups)
        ->on('group_name')
        ->source('group')
        ->direction('left')
);
```

Here, `group` from the origil users is matched against `group_name` from the groups. The left join retains the original users even if a match is absent.

The joined result includes columns from both inputs. Use `ExtractOperation` afterward to select the columns required by the target table.

### UnionOperation

Combines selected columns from two operation results. The selected columns are paired by position, so their names may differ.

```php
$combined = $opr::register(
    UnionOperation::from($firstOperation)
        ->union($secondOperation)
        ->sourceColumns(['id', 'name'])
        ->targetColumns(['user_id', 'display_name'])
);
```

The example illustrates the intended selection pattern; check the operation's current implementation for the exact supported method signatures before using it. The resulting columns follow the first input's selected columns.


## Collection
Although individual operations are easy to understand, it becomes hard work if you want to manage quite some big changes. However as long as it is registered in `OperationManager` you can bastract some of the operations, although order matters.

There is no "OperationCollection" class or anything. But you can wrap some typical manipulation to a block.

```php
$opr = R3T::operator();

// ... other operations

/**
 * @return Operation[] The original table's modified operation and the newly created table's operation
 */
function extractColumnAndMakeIntoNewTableThenReferenceTheId(OperationManager $opr, Operation $original, string $column, string $targetColName):array{
    $extract = $opr::register(ExtractOperation::from($original)->extract($column));
    $distinct = $opr::register(DistinctOperation::from($extract)->distinct($column));
    $rename = $opr::register(RenameOperation::from($distinct)->rename([$column$ => $targetColName]));
    $temp_id = $column.'_id'; // make temporary name to avoid conflict on later join
    $addId = $opr::register(AddColumnsOperation::from($rename)->add([$temp_id]));
    $populateId = $opr::register(ModifyValuesOperation::from($addId)->modify(function(iterable $records){
        $i = 1;
        foreach($records as $record){
            $record[$temp_id] = $i;
            $i++;
            yield $record;
        }
    }));
    $joinToSyncId = $opr::register(JoinOperation::from($original)->join($populateId)->on($targetColName)->source($column)->direction('left')); // beware it contains cols from both tables, so you need to extract later. you cannot determine all the column names at this stage.

    $groupsAdjust = $opr::register(RenameOperation::from($populateId)->rename([$temp_id=>'id'])); // rename to normalize

    return [$joinToSyncId, $groupsAdjust];
}
```

Operations can refer to earlier results, and operations such as joins can depend on another operation as well. This defines the relationships between transformations.

## Extending the Operation Class
You can extend the Operation class to implement your own operation if you feel like it. See [Extending the Operation Class]() for more details.