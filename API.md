
# Class: Nether\Datastore

## Dataset API #################################################################

#### `->Import(iterable): $this`

Import the supplied data to use. Any defined Import Filters will be applied to
the data as it is imported.

```php
$Data->Import([ 'One'=> 1, 'Two'=> 2, 'Three'=> 3 ]);
```

#### `->Load(string $Filename): $this`

Import data from the spcified file on disk. Any defined Import Filters will be
applied to the data as it is loaded. If the filename ends with `.json` it is
expected to be a valid JSON object-array. Otherwise it is expected to be a
valid PHSON file (digestable by PHP's unserialize()).

--------------------------------------------------------------------------------

#### `->Export(void): array`

Export the current working dataset as an array.

```php
$Raw = $Data->Export();
```

#### `->Write(string $Filename): $this`

Export the current working dataset to the specified file on disk. If the
filename ends with `.json` it will write a JSON format file, otherwise it will
write a PHSON file (output from PHP's serialize()).

## Filtering API #################################################################

By default no filtering is done to the contents of a datastore. There are two
types of filters that can be used: Value (runs when a value is fetched) and
Import (runs when a value is stored).

#### `->SetValueFilter(string $Key, callable|array $Func): $this`
#### `->UnsetValueFilter(string $Key, callable|array $Func): $this`
#### `->SetImportFilter(string $Key, callable|array $Func): $this`
#### `->UnsetImportFilter(string $Key, callable|array $Func): $this`
