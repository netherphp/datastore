# Class: Nether\Datastore ######################################################

In addition to the following API, the Datastore class implements the following
SPL interfaces:

* `ArrayAccess`
* `Countable`
* `Iterator`

## Getting Started #############################################################

The primary way to initialise a datastore object is to give it an iterable thing
of things.

```php
$Data = new Datastore([ ... ]);
```

```php
$Data = Datastore::New([ ... ]);
```

There are additional factory methods for more specific cases.


```php
// from a file on disk
$Data = Datastore::FromFile('cache/posts.json');
```

```php
// from an exploded string
$Data = Datastore::FromString('asdf');
```

```php
// from a delimited string
$Data = Datastore::FromString('a|s|d|f', '|');
```

```php
// from a merged set of lists
$Data = Datastore::FromStackMerge($List1, $List2, $List3);
```

```php
// from a blended set of lists
$Data = Datastore::FromStackBlend($List1, $List2, $List3);
```

--------------------------------------------------------------------------------

There are two events fired during construction, one before and one after the
supplied data is stored within itself. Classes can extend the datastore, and
override these methods to preconfigure any needs.

#### `->OnPrepare(void): void`

Called before the data is Imported. This would be a good place to define any
import filters needed.

#### `->OnReady(void): void`

Called after the data is imported. This could used to post process the imported
data to fit whatever needs.

## Dataset API #################################################################

#### `->Import(iterable $Input): $this`

Import the supplied data to use. Any defined Import Filters will be applied to
the data as it is imported.

```php
$Data->Import([ 'One'=> 1, 'Two'=> 2, 'Three'=> 3 ]);
```

#### `->Load(string $Filename): $this`

Import data from the sepcified file on disk. Any defined Import Filters will be
applied to the data as it is loaded.

```php
$Data->Load('cache/posts.json');
```

> If the file ends with `.json` it is expected to be a valid JSON object.
> Otherwise it is expected to be valid PHSON (from PHP's built-in serialize()).

--------------------------------------------------------------------------------

#### `->Export(void): array`

Export the current working dataset as an array.

```php
$Raw = $Data->Export();
```

#### `->Write(string $Filename): $this`

Export the current working dataset to the specified file on disk.

```php
$Data->Write('cache/posts.json');
```

> If the file ends with `.json` it will write a JSON format file.
> Otherwise it will write PHSON format (from PHP's built-in serialize()).

## Filtering API ###############################################################

There are two types of filters that can be used: Value (runs when a value is
fetched) and Import (runs when a value is stored). There are no filters by
default.

* Value filters can be defined at any time as they do not alter the stored
  data, rather executed each time the value is read out.

* Import Filters should be defined before the datastore is populated.
  Values already stored within will not be filtered until the next time
  they are updated.

#### `->SetValueFilter(string $Key, callable|array $Func): $this`

Set a Value Filter for a specific key. Can take either a callable function or
a list of them to run in series. Data will be run through the filters each
time they are read.

```php
// make sure we always get an integer back.
$Data->Set('ID', $_GET['id']);
$Data->SetValueFilter('ID', fn(mixed $V)=> (int)$V);

var_dump($Data->Get('ID'));
```

> $Func: fn(mixed $Value): mixed

#### `->UnsetValueFilter(string $Key): $this`

Unset Value Filters for a specific key.

```php
// make sure we always get an integer back.
$Data->UnsetValueFilter('ID');
```

--------------------------------------------------------------------------------

#### `->SetImportFilter(string $Key, callable|array $Func): $this`

Set Import Filters for a specific key. Can take either a callable function or
a list of them to run in series. Data will be run through the defined filters
when they are added or updated.

```php
// make sure we only ever store an integer here.
$Data->SetImportFilter('ID', fn(mixed $V)=> (int)$V);
$Data->Set('ID', $_GET['id']);

var_dump($Data->Get('ID'));
```

> $Func: fn(mixed $Value): mixed

#### `->UnsetImportFilter(string $Key): $this`

Unset Import Filters for a specific key.

```php
$Data->UnsetImportFilter('ID');
```

## Protection API ##############################################################

The protection API is an aid to guard values from being dumped by debugging
tools like `var_dump()` and `print_r()`. The data is not altered in any way, 
only masked when printed by anything using PHP's magic `__debugInfo` system.

Use case: about to `var_dump()` something you probably shouldn't, like a
configuration, but nothing can stop it because you are an agent of chaos.
Someone smarter than you can use this to flag things like misc API keys when
they define them, to try and avoid you shooting yourself later.

#### `->SetProtectedKey(int|string $Key, bool $Strip=FALSE): $this`

Add a key to protect from debugging info.

```php
$Data->SetProtectedKey('APIKEY');
var_dump($Data);
```

> If `$Strip` is FALSE it will print "`[protected thing]`" in place of the value.

> If `$Strip` is TRUE it will completely omit the key as if it does not exist.

#### `->UnsetProtectedKey(int|string $Key): $this`

Remove a key from debugging protection.

```php
$Data->UnsetProtectedKey('APIKEY');
var_dump($Data);
```

## Input/Output API ############################################################

#### `->Set(int|string $Key, mixed $Value): $this`

Set and or overwrite the specified key to the specified value. Value will be transformed by any Import Filters that were defined for that key before stored.

```php
$Data->Set('ID', 42);
```

#### `->Define(int|string $Key, mixed $Value): $this`

Same as `Set()` except if the key already exists, it will NOT be overwritten.
Useful for setting default config values late if some user config has already
been supplied.

```php
$Data->Define('ID', 69);
```

#### `->Unset(int|string $Key): $this`

Removes the key and value from the datastore.

```php
$Data->Unset('ID');
```

--------------------------------------------------------------------------------

#### `->Count(void): int`

Return how many items are stored.

```php
$Num = $Data->Count();
```

#### `->Has(int|string $Key): bool`

Check if a key exists. Does not care what the value of that key is, only that
we have one.

```php
if($Data->Has('ID'))
printf('dataset contains an ID.%s', PHP_EOL);
```

#### `->Get(int|string $Key): mixed`

Fetch a specific value. Value will be transformed by any Value Filters that
were defined before it hands it back to you.

```php
printf('ID: %d.%s', $Data->Get('ID'), PHP_EOL);
```

#### `->Pick(int|string ...$Keys): Datastore`

Fetch a list of values. Values will be transformed by any Value Filters that
were defined before it hands them back to you.

```php
$List = $Data->Pick('One', 'Two', 'Three');
printf('picked %d things.%s', $List->Count(), PHP_EOL);
```

--------------------------------------------------------------------------------

#### `->Push(mixed $Item): $this`
#### `->PushFirst(mixed $Item): $this`

Append an item to either end of the dataset.

```php
$Users->Push($Who);
```

#### `->Pop(mixed $Item): $this`
#### `->PopFirst(mixed $Item): $this`

Pop off an item from either end of the dataset.

```php
$Who = $Users->Pop();
```

## Data Processing API #########################################################

#### `->SortByKey(?callable $Func=NULL): $this`;
#### `->SortByValue(?callable $Func=NULL): $this`;

Sort an array with the specified callable. If no callable provided then uses
the default PHP ksort() and asort() functions.

```php
$Users->Sort(fn(User $A, User $B)=> $A->Username <=> $B->Username);
```

--------------------------------------------------------------------------------

#### `->Each(callable $Func): $this`

> $Func: fn(mixed $Value) => void

#### `->EachKeyValue(callable $Func): $this`

> $Func: fn(mixed $Key, mixed $Value) => void

Run a callable for each item in the dataset.

```php
// debug dump of users.
$Users->Each(fn(User $Who)=> var_dump($Who));
```

--------------------------------------------------------------------------------

#### `->Map(callable $Func): Datastore`

> $Func: fn(mixed $Value) => mixed

#### `->MapKeyValue(callable $Func): Datastore`

> $Func: fn(mixed $Key, mixed $Value) => mixed

Map the dataset to a new Datastore object using the specified callable.

```php
// make a list of email addresses.
$Emails = $Users->Map(fn(Users $Who)=> $Who->Email));
```

--------------------------------------------------------------------------------

#### `->Remap(callable $Func): $this`

> $Func: fn(mixed $Value) => mixed

#### `->RemapKeyValue(callable $Func): $this`

> $Func: fn(mixed $Key, mixed $Value) => mixed

Map this dataset in place using the specified callable.

```php
// just keep a list of email addresses.
$Users->Remap(fn(Users $Who)=> $Who->Email));
```

--------------------------------------------------------------------------------

#### `->Distill(callable $Func): Datastore`

> $Func: fn(mixed $Value) => bool

#### `->DistillKeyValue(callable $Func): Datastore`

> $Func: fn(mixed $Key, mixed $Value) => bool

Distill the dataset into a new Datastore object using the specified callable
filter function. Returning TRUE from the callable keeps the item, returning
FALSE discards it.

```php
// get a list of only users who opt in.
$OptedIn = $Users->Distill(fn(User $Who)=> $Who->HasOptedIn('email'));
```

--------------------------------------------------------------------------------

#### `->Filter(callable $Func): $this`

> $Func: fn(mixed $Value) => bool

#### `->FilterKeyValue(callable $Func): $this`

> $Func: fn(mixed $Key, mixed $Value) => bool

Filter the dataset in place using the specified callable filter function Returning TRUE from the callable keeps the item, returning FALSE discards it.

```php
// keep only users who opt in
$Users->Filter(fn(User $Who)=> $Who->HasOptedIn('email'));
```

--------------------------------------------------------------------------------

#### `->Reindex(void): $this`
#### `->ReindexKeyValue(callable $Func): $this`

> $Func: fn(mixed $Key, mixed $Value) => bool

Reindex this dataset. The simple version will turn it into a simple numerically
sequenced list. The complex version has a few different behaviours depending on
the return supplied callable.

	* NULL
	Nothing happens. Key and value remain unchanged.

	* bool FALSE
	Key and data is removed from the dataset completely.
	
	* string <key>
	Data is moved to the new key name.
	
	* array [ <key>, <value> ]
	Data is moved to the new key name, and the value overwritten with the
	supplied value.

```php
// listify dataset after filtering left indexing gaps.
$Data->Reindex();
```

```php
// snake_case to PascalCase the dataset key names.
$Data->ReindexKeyValue(
	fn(string $Key, mixed $Val)
	=> str_replace(' ', '', ucwords(str_replace('_', ' ', $Key)))
);
```

--------------------------------------------------------------------------------

#### `->Roll(callable $Func, mixed $Initial=0): mixed`

> $Func: fn(mixed $Value, mixed $Prev) => bool

#### `->RollKeyValue(callable $Func, mixed $Initial=0): mixed`

> $Func: fn(mixed $Key, mixed $Value, mixed $Prev) => bool

Perform a rollup operation (accumulation / reduce) by walking across all items
carrying a value that they can alter with the supplied callable.

```php
// calculate total points in current economy.
$Total = $Users->Roll(fn(User $Who, int $Prev)=> $Prev + $Who->Points);
```

## Misc Utility ################################################################

#### `->Join(string $Delim): string`

Join each item together into a giant string using the specified delimiter.

```php
// get a pipe delimited list of things.
$Row = $Data->Join('|');
```

#### `->Shuffle(void): $this`

Randmise the order of this dataset.

```php
$Data->Shuffle();
```

--------------------------------------------------------------------------------

#### `->IsEmpty(void): bool`

Test if the dataset is empty.

```php
if($Data->IsEmpty())
echo 'Dataset is empty.', PHP_EOL;
```

#### `->IsTrue(string $Key): bool`

Test if the specified item is boolean TRUE.

```php
if($Config->IsTrue('something'))
PerformFeatureSomething();
```

#### `->IsTrueEnough(string $Key): bool`

Test if the specified value is boolean TRUE, or something that evaluates to
true. This test differs from PHP's default handlng of what is trueish.

> TRUE, 1, '1', 'T', 'TRUE', 'Y', 'YES' [case-insensitive]

```php
if($Config->IsTrueEnough('something'))
PerformFeatureSomething();
```

#### `->IsFalse(string $Key): bool`

Test if the specified item is boolean FALSE.

```php
if($Config->IsFalse('something')) {
	echo 'skipping feature: Something', PHP_EOL;
	return;
}
```

#### `->IsFalseEnough(string $Key): bool`

Test if the specified value is boolean FALSE, or something that evaluates to
false. This test differs from PHP's default handlng of what is falsy.

> NULL, FALSE, 0, '0', 'F', 'FALSE', 'N', 'NO' [case-insensitive]

```php
if($Config->IsFalseEnough('something')) {
	echo 'skipping feature: Something', PHP_EOL;
	return;
}
```

> (also anything not coverved by IsTrueEnough, example: 'asdf')

#### `->IsNull(string $Key): bool`

Test if the specified item is literal NULL and/or undefined.

```php
if($Config->IsNull('something')) {
	echo 'missing configuration for something', PHP_EOL;
	return;
}
```

#### `->IsNullEnough(string $Key): bool`

Test if the specified value is literal NULL, or something that evaluates to
NULL. This test differs from PHP's default handlng of what is nullish.

> NULL, '', 'NULL' [case-insensitive]

```php
if($Config->IsNullEnough('something')) {
	echo 'missing configuration for something', PHP_EOL;
	return;
}
```

--------------------------------------------------------------------------------

#### `->GetKeys(): Datastore`

#### `->GetValues(): Datastore`

#### `->IsFirstKey(string $Key): bool`

#### `->IsLastKey(string $Key): bool`

#### `->GetFirstKey(void): bool`

#### `->GetLastKey(void): bool`

#### `->GetFirstValue(void): bool`

#### `->GetLastValue(void): bool`

