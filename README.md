# NetherPHP: Datastore

[![Packagist](https://img.shields.io/packagist/v/netherphp/datastore.svg?style=for-the-badge)](https://packagist.org/packages/netherphp/datastore)
[![Build Status](https://img.shields.io/github/actions/workflow/status/netherphp/datastore/phpunit.yml?style=for-the-badge)](https://github.com/netherphp/datastore/actions)
[![codecov](https://img.shields.io/codecov/c/gh/netherphp/datastore?style=for-the-badge&token=VQC48XNBS2)](https://codecov.io/gh/netherphp/datastore)

Provides a data collection object that is a redux of the Nether\Common\Datastore rebuilt with the lessons learned from the original. It's primary purpose is to make PHP's API more sane by having consistent callable argument orders, naming, etc, whilst working with arrays of data.



# Documentation

[API Dump in Github Wiki](https://github.com/netherphp/datastore/wiki/API:-Nether%5CDatastore)



# File API

### `->Load(string $Filename): $this`

Populate this datset from the specified file. If the filename ends with `.json` then it loads expecting JSON format. Otherwise it assumes PHP's serialise format.

### `->Write(string $Filename): $this`

Store this dataset to the specified file on disk. If the filename ends with `.json` then it will write a JSON formatted file. Otherwise it will write in PHP's serialize format.



# Basic API

### `->Import(iterable $Values): $this`

Set/Overwrite entire dataset with specified data.

### `->Export(): array`

Get entire dataset as an array.

----

### `->Has(mixed $Key): bool`

Checks if dataset has specified key.

### `->Get(mixed $Key): mixed`

Get value for specified key.

### `->Set(mixed $Key, mixed $Value): $this`

Set value for specified key.

### `->Define(mixed $Key, mixed $Value): $this`

Set value for specified key only if it has not already in this dataset. Otherwise the old value is kept.

### `->Unset(mixed $Key): $this`

Remove specified key from dataset.

----

### `->Push(mixed $Value): $this`
### `->PushFirst(mixed $Value): $this`

Add an item to either end of the dataset.

### `->Pop(mixed $Value): mixed`
### `->PopFirst(mixed $Value): mixed`

Pop off and return an item from either end of the dataset.

---

### `->Merge(iterable $Values): $this`
### `->MergeStart(iterable $Values): $this`

Append items to either end of the dataset, overwriting the original values of any keys that already existed in the dataset.

### `->Blend(iterable $Values): $this`
### `->BlendStart(iterable $Values): $this`

Append items to either end of the dataset, keeping the original values of any keys that already existed in the dataset.



# Data Processing API

### `->Each(callable fn(mixed $Value)): $this`
### `->EachKeyValue(callable fn(mixed $Key, mixed $Value)): $this`

Iterate the entire dataset running the specified callable. The return value of the callable is unused, the dataset unmodified.

```php
// debug everyone
$Data->Each(fn(User $Who)=> var_dump($Who));
```

----

### `->Map(callable fn(mixed $Value)): new`
### `->MapKeyValue(callable fn(mixed $Key, mixed $Value)): new`

Return a new dataset as mapped by the specified callable. The return value is what becomes the value for each key.

```php
// fetch list of email addresses.
$Emails = $Data->Map(fn(User $Who)=> $Who->Email);
```

### `->Remap(callable fn(mixed $Value)): $this`
### `->RemapKeyValue(callable fn(mixed $Key, mixed $Value)): $this`

Modify this dataset as mapped by the specified callable. The return value is what becomes the value for each key.

```php
// trim list to just email addresses.
$Data->Remap(fn(User $Who)=> $Who->Email);
```

----

### `->Distill(callable fn(mixed $Value)): new`
### `->DistillKeyValue(callable fn(mixed $Key, mixed $Value)): new`

Return a new dataset as filtered by the specified callable. Returning TRUE will keep the data and returning FALSE will omit the data.

```php
// list users who have opted in.
$OptedIn = $Data->Distill(fn(User $Who)=> $Who->HasOptedIn());
```

### `->Filter(callable fn(mixed $Value)): $this`
### `->FilterKeyValue(callable fn(mixed $Key, mixed $Value)): $this`

Modify this dataset as filtered by the specified callable. Returning TRUE will keep the data and returning FALSE will remove the data.

```php
// keep users who opted in
$Data->Filter(fn(User $Who)=> $Who->HasOptedIn());
```

----

### `->Roll(callable fn(mixed $Value, mixed $Carry), mixed $Initial=0): mixed`
### `->RollKeyValue(callable fn(mixed $Key, mixed $Value, mixed $Carry), mixed $Initial=0): mixed`

Perform a rollup/accumulation/reduce action on this dataset. Returns the specified initial value after it has been carried and modified by each item in the dataset.

```php
// sum of points in entire economy.
$PointsInEconomy = $Data->Roll(
	fn(User $Who, int $Total)
	=> $Total + $Who->Points
);
```

----

### `->Reindex(void): $this`
### `->ReindexKeyValue(callable fn(mixed $Key, mixed $Value)): $this`

Reindex this dataset. The simple method will convert the data into a proper numerically ordered list.

```php
// reindex this list.
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
$Data->SortByValue(
	fn(User $A, User $B)
	=> $A->Username <=> $B->Username
);
```



# Misc Dataset Util API

### `->IsEmpty(void): bool`

Is this dataset empty?

### `->IsList(void): bool`

Is this dataset a proper numeric indexed list?



# Misc Value Util API

### `->IsTrue(string $Key): bool`

Test the specified value is boolean TRUE.

### `->IsTrueEnough(string $Key): bool`

Test the specified value as boolean TRUE or a value that seems to true enough. It has strict context of what is allowed to be considered trueish via Filter\Type::Bool() which differs from PHP's more broad behaviour.

- `bool(TRUE)`
- `int(1)`
- `string('1', 'T', 'TRUE', 'Y', 'YES') [case-insensitive]`

### `->IsFalse(string $Key): bool`

Test the specified value is boolean FALSE.

### `->IsFalseEnough(string $Key): bool`

Test the specified value as boolean FALSE or a value that seems false enough. It has strict context of what is allowed to be considered falsey via Filter\Type::Bool() which differs from PHP's more broad behaviour.

- `bool(FALSE)`
- `int(0)`
- `string('', '0', 'F', 'FALSE', 'N', 'NO') [case-insensitive]`

### `->IsNull(void): bool`

Test the specified value as literal NULL.

### `->IsNullEnough(void): bool`

Test the specified value as literal NULL or null enough.

- `NULL`
- `int(0)`
- `string('', 'NULL') [case-insensitive]`



# Protected Keys API

Protect data from being accidentally dumped by debugging tools such as `var_dump()` and `print_r()`. The true values will still exist in the dataset and can be read normally with the other methods of the Datastore object.

### `->AddProtectedKey(string $Key, bool $Strip=FALSE)`

Add a specific key to the debug protection.

> If `$Strip` is false then the value for this key will be blotted out by the text "[protected type]".

>if `$Strip` is true then the key and value will be fully omitted from the debug view.

### `->RemoveProtectedKey(string $Key)`

Remove a specific key from the debug protection.

### `->ClearProtectedKey(string $Key)`

Remove all previously added keys from the debug protection.



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
