# NetherPHP: Datastore

[![Packagist](https://img.shields.io/packagist/v/netherphp/datastore.svg?style=for-the-badge)](https://packagist.org/packages/netherphp/datastore)
[![Build Status](https://img.shields.io/github/actions/workflow/status/netherphp/datastore/phpunit.yml?style=for-the-badge)](https://github.com/netherphp/datastore/actions)
[![codecov](https://img.shields.io/codecov/c/gh/netherphp/datastore?style=for-the-badge&token=VQC48XNBS2)](https://codecov.io/gh/netherphp/datastore)

Provides a data collection object that is a redux of the Nether\Common\Datastore rebuilt with the lessons learned from the original. It's primary purpose is to make PHP's API more sane by having consistent callable argument orders, naming, etc, whilst working with arrays of data.



# Documentation

In addition to the following API, the Datastore class implements the following SPL interfaces:

* ArrayAccess
* Countable
* Iterator

[API Dump in Github Wiki](https://github.com/netherphp/datastore/wiki/API:-Nether%5CDatastore)



# File API

### `->Load(string $Filename): $this`

Populate this datset from the specified file. If the filename ends with `.json` then it loads expecting JSON format. Otherwise it assumes PHP's serialise format. Throws exceptions for read failures.

```php
$Data->Load('cache/post-list.json');
```

### `->Write(string $Filename): $this`

Store this dataset to the specified file on disk. If the filename ends with `.json` then it will write a JSON formatted file. Otherwise it will write in PHP's serialize format. Throws exceptions for write failiures.

```php
$Data->Write('cache/post-list.json');
```



# Basic Data API

### `->Import(iterable $Values): $this`

Set/Overwrite entire dataset with specified data.

```php
$Data->Import([ 'Key1' => 1, 'Key2' => 2, 'Key3' => 3 ]);
```

### `->Export(): array`

Get entire dataset as an array.

```php
$Raw = $Data->Export();
```

----

### `->Count(void): int`

Count how many items are in this dataset.

```php
if($Data->Count() > 0)
echo 'dataset is not empty', PHP_EOL;
```

### `->Has(mixed $Key): bool`

Checks if dataset has specified key.

```php
if($Data->Has('Key1'))
echo 'data for Key1 exists', PHP_EOL;
```

### `->Get(mixed $Key): mixed`

Get value for specified key.

```php
$Val = $Data->Get('Key1');
```

### `->Set(mixed $Key, mixed $Value): $this`

Set value for specified key.

```php
$Data->Set('Key1', 'New Value');
```

### `->Define(mixed $Key, mixed $Value): $this`

Set value for specified key only if it has not already in this dataset. Otherwise the old value is kept.

```php
$Data->Define('Key1', 'Potential New Value');
```

### `->Unset(mixed $Key): $this`

Remove specified key from dataset.

```php
$Data->Unset('Key1');
```

----

### `->Push(mixed $Value): $this`
### `->PushFirst(mixed $Value): $this`

Append an item to either end of the dataset.

```php
// append user to dataset.
$Users->Push($Who);
```

### `->Pop(void): mixed`
### `->PopFirst(void): mixed`

Pop off and return an item from either end of the dataset.

```php
// pull the last item off the dataset.
$Who = $Users->Pop();
```

----

### `->Merge(iterable $Values): $this`
### `->MergeStart(iterable $Values): $this`

Append items to either end of the dataset, overwriting the original values of any keys that already existed in the dataset.

```php
// merge in this additional dataset.
$Data->Merge([ 'Key4'=> 'Data4', ... ])
```

### `->Blend(iterable $Values): $this`
### `->BlendStart(iterable $Values): $this`

Append items to either end of the dataset, keeping the original values of any keys that already existed in the dataset.

```php
// blend in this additional dataset.
$Data->Blend([ 'Key4'=> 'Data4', ... ])
```



# Data Processing API

### `->Each(callable fn(mixed $Value)): $this`
### `->EachKeyValue(callable fn(mixed $Key, mixed $Value)): $this`

Iterate the entire dataset running the specified callable. The return value of the callable is unused, the dataset unmodified.

```php
// debug everyone
$Users->Each(fn(User $Who)=> var_dump($Who));
```

----

### `->Map(callable fn(mixed $Value)): new`
### `->MapKeyValue(callable fn(mixed $Key, mixed $Value)): new`

Return a new dataset as mapped by the specified callable. The return value is what becomes the value for each key.

```php
// fetch list of email addresses.
$Emails = $Users->Map(fn(User $Who)=> $Who->Email);
```

### `->Remap(callable fn(mixed $Value)): $this`
### `->RemapKeyValue(callable fn(mixed $Key, mixed $Value)): $this`

Modify this dataset as mapped by the specified callable. The return value is what becomes the value for each key.

```php
// trim list to just email addresses.
$Users->Remap(fn(User $Who)=> $Who->Email);
```

----

### `->Distill(callable fn(mixed $Value)): new`
### `->DistillKeyValue(callable fn(mixed $Key, mixed $Value)): new`

Return a new dataset as filtered by the specified callable. Returning TRUE will keep the data and returning FALSE will omit the data.

```php
// list users who have opted in.
$OptedIn = $Users->Distill(fn(User $Who)=> $Who->HasOptedIn());
```

### `->Filter(callable fn(mixed $Value)): $this`
### `->FilterKeyValue(callable fn(mixed $Key, mixed $Value)): $this`

Modify this dataset as filtered by the specified callable. Returning TRUE will keep the data and returning FALSE will remove the data.

```php
// keep users who opted in
$Users->Filter(fn(User $Who)=> $Who->HasOptedIn());
```

----

### `->Roll(callable fn(mixed $Value, mixed $Carry), mixed $Initial=0): mixed`
### `->RollKeyValue(callable fn(mixed $Key, mixed $Value, mixed $Carry), mixed $Initial=0): mixed`

Perform a rollup/accumulation/reduce action on this dataset. Returns the specified initial value after it has been carried and modified by each item in the dataset.

```php
// sum of points in entire economy.
$PointsInEconomy = $Users->Roll(
	fn(User $Who, int $Total)
	=> $Total + $Who->Points
);
```

----

### `->Reindex(void): $this`
### `->ReindexKeyValue(callable fn(mixed $Key, mixed $Value)): $this`

Reindex this dataset. The simple method will convert the data into a proper numerically ordered list.

```php
// numerically reindex this list.
$Data->Reindex();
```

The complex method has a few different behaviours depending on the return value of the callable:

	* NULL
	Nothing happens. Key and value remain unchanged.

	* bool FALSE
	Key and data is removed from the dataset completely.
	
	* string <key>
	Data is moved to the new key name.
	
	* array [ <key>, <value> ]
	Data is moved to the new key name, and the value overwritten with the supplied value.

```php
// rename snake_case keys to PascalCase keys.
$Data->ReindexKeyValue(
	fn(string $Key, mixed $Val)
	=> str_replace(' ', '', ucwords(str_replace('_', ' ', $Key)))
);
```

----

### `->SortByKey(callable fn(mixed $A, mixed $B)): $this`
### `->SortByValue(callable fn(mixed $A, mixed $B)): $this`

Sort this dataset using the specified callable.

```php
// sort users by their username
$Users->SortByValue(
	fn(User $A, User $B)
	=> $A->Username <=> $B->Username
);
```



# Dataset Util API

### `->IsEmpty(void): bool`

Is this dataset empty?

```php
if($Users->IsEmpty())
echo 'no users found.', PHP_EOL;
```

### `->IsList(void): bool`

Is this dataset a proper numeric indexed list?

```php
if($Users->IsList())
echo 'it is a list (non-associative-array).', PHP_EOL;
```



# Value Util API

### `->IsTrue(string $Key): bool`

Test the specified value as boolean TRUE.

```php
if($Data->IsTrue('Something'))
echo 'Something is true', PHP_EOL;
```

### `->IsTrueEnough(string $Key): bool`

Test the specified value as boolean TRUE or a value that seems to true enough. It has strict context of what is allowed to be considered trueish via Filter\Type::Bool() which differs from PHP's more broad behaviour.

- `bool(TRUE)`
- `int(1)`
- `string('1', 'T', 'TRUE', 'Y', 'YES') [case-insensitive]`

```php
if($Data->IsTrueEnough('Something'))
echo 'Something is trueish enough to say its true.', PHP_EOL;
```

### `->IsFalse(string $Key): bool`

Test the specified value as boolean FALSE.

```php
if($Data->IsFalse('Something'))
echo 'Something is false', PHP_EOL;
```

### `->IsFalseEnough(string $Key): bool`

Test the specified value as boolean FALSE or a value that seems false enough. It has strict context of what is allowed to be considered falsey via Filter\Type::Bool() which differs from PHP's more broad behaviour.

- `bool(FALSE)`
- `int(0)`
- `NULL`
- `string('', '0', 'F', 'FALSE', 'N', 'NO') [case-insensitive]`
- `string(technically *any* string that fails IsTrueEnough)`

```php
if($Data->IsFalseEnough('Something'))
echo 'Something is falsey enough to say its false.', PHP_EOL;
```

### `->IsNull(void): bool`

Test the specified value as literal NULL.

```php
if($Data->IsNull('Something'))
echo 'Something is null', PHP_EOL;
```

### `->IsNullEnough(void): bool`

Test the specified value as literal NULL or null enough.

- `NULL`
- `string('', 'NULL') [case-insensitive]`

```php
if($Data->IsNullEnough('Something'))
echo 'Something is nullish enough to say its null.', PHP_EOL;
```



# Protected Keys API

Protect data from being accidentally dumped by debugging tools such as `var_dump()` and `print_r()`. The true values will still exist in the dataset and can be read normally with the other methods of the Datastore object.

### `->AddProtectedKey(string $Key, bool $Strip=FALSE)`

Add a specific key to the debug protection.

> If `$Strip` is false then the value for this key will be blotted out by the text "[protected type]".

> if `$Strip` is true then the key and value will be fully omitted from the debug view.

```php
$Data->AddProtectedKey('Key1');
var_dump($Data);
```

### `->RemoveProtectedKey(string $Key)`

Remove a specific key from the debug protection.

```php
$Data->RemoveProtectedKey('Key1');
var_dump($Data);
```

### `->ClearProtectedKeys(string $Key)`

Remove all previously added keys from the debug protection.

```php
$Data->ClearProtectedKeys('Key1');
var_dump($Data);
```

# TODO

* Move Nether\Meta namespace to `netherphp/meta` once base annotations are re-established.

* Move Nether\Filters namespace to `netherphp/filter` once base filters are re-implemented.

* Dependencies: Nether\Datastore
	- netherphp/filter
	- netherphp/meta

* Dependencies: Nether\Filters
	- netherphp/meta

* Dependencies: Nether\Meta
	- none
