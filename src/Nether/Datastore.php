<?php ##########################################################################
################################################################################

namespace Nether;

use ArrayAccess;
use Iterator;
use Countable;
use Exception;

use Nether\Meta\Code;
use Nether\Filter;

################################################################################
################################################################################

class Datastore
implements
	ArrayAccess,
	Countable,
	Iterator {

	#[Code\Info('Internal dataset.')]
	#[Code\Added('2026-10-26')]
	protected array
	$Dataset;

	################################################################
	################################################################

	use
	Datastore\DatasetFilterPackage,
	Datastore\DatasetProtectionPackage;

	################################################################
	################################################################

	#[Code\Added('2026-10-26')]
	public function
	__Construct(iterable $Input=[]) {

		$this->OnPrepare();
		$this->Import($Input);
		$this->OnReady();

		return;
	}

	################################################################
	################################################################

	#[Code\Info('To be overwritten by extending classes to allow things before import.')]
	#[Code\Added('2026-11-05')]
	public function
	OnPrepare():
	void {

		return;
	}

	#[Code\Info('To be overwritten by extending classes to allow things after import.')]
	#[Code\Added('2026-11-05')]
	public function
	OnReady():
	void {

		return;
	}

	################################################################
	################################################################

	#[Code\Info('Populate the internal dataset with the specified data.')]
	#[Code\Added('2026-10-26')]
	public function
	Import(iterable $Input):
	static {

		$Data = match(TRUE) {
			(!is_array($Input))
			=> iterator_to_array($Input),

			default
			=> $Input
		};

		$this->ImposeImportFiltersOn($Data);

		$this->Dataset = $Data;

		return $this;
	}

	#[Code\Info('Populate the internal dataset from a file on disk (.json or .phson).')]
	#[Code\Added('2026-10-29')]
	public function
	Load(string $Filename):
	static {

		if(!file_exists($Filename))
		throw new Datastore\Error\FileNotFound($Filename);

		if(!is_readable($Filename))
		throw new Datastore\Error\FileAccessUnreadable($Filename);

		////////

		if(str_ends_with($Filename, '.json'))
		$this->LoadJSON($Filename);
		else
		$this->LoadPHSON($Filename);

		return $this;
	}

	#[Code\Added('2026-10-29')]
	protected function
	LoadJSON(string $Filename):
	void {

		$Data = json_decode(file_get_contents($Filename), TRUE);

		if(!is_array($Data))
		throw new Datastore\Error\FileFormatInvalid($Filename);

		////////

		$this->Import($Data);

		return;
	}

	#[Code\Added('2026-10-29')]
	protected function
	LoadPHSON(string $Filename):
	void {

		// unserialise still emits warnings instead of throwing for
		// the most basic case of invalid data smh

		$Data = @unserialize(file_get_contents($Filename));

		if(!is_array($Data))
		throw new Datastore\Error\FileFormatInvalid($Filename);

		////////

		$this->Import($Data);

		return;
	}

	////////////////////////////////////////////////////////////////

	#[Code\Info('Return the internal dataset array.')]
	#[Code\Added('2026-10-26')]
	public function
	Export():
	array {

		return $this->Dataset;
	}

	#[Code\Info('Write this dataset to a file on disk (.json or .phson).')]
	#[Code\Added('2026-10-29')]
	public function
	Write(string $Filename):
	static {

		if(!file_exists($Filename)) {
			if(!is_writable(dirname($Filename)))
			throw new Datastore\Error\FileAccessUnwritable(dirname($Filename));
		}

		elseif(!is_writable($Filename))
		throw new Datastore\Error\FileAccessUnwritable($Filename);

		////////

		if(str_ends_with($Filename, '.json'))
		$this->WriteJSON($Filename);
		else
		$this->WritePHSON($Filename);

		return $this;
	}

	#[Code\Added('2026-10-29')]
	protected function
	WriteJSON(string $Filename):
	void {

		file_put_contents(
			$Filename,
			json_encode($this->Export())
		);

		return;
	}

	#[Code\Added('2026-10-29')]
	protected function
	WritePHSON(string $Filename):
	void {

		file_put_contents(
			$Filename,
			serialize($this->Export())
		);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Code\Info('Check if the dataset has had anything put into it.')]
	#[Code\Added('2026-10-31')]
	public function
	IsEmpty():
	bool {

		return ($this->Count() === 0);
	}

	#[Code\Info('Check if this dataset is a "list" meaning cleanly numerically indexed.')]
	#[Code\Added('2026-10-27')]
	public function
	IsList():
	bool {

		return array_is_list($this->Dataset);
	}

	#[Code\Info('Check if the specified key is the first in this dataset.')]
	#[Code\Added('2026-11-02')]
	public function
	IsFirstKey(int|string $Key):
	bool {

		return ($this->GetFirstKey() === $Key);
	}

	#[Code\Info('Check if the specified key is the last in this dataset.')]
	#[Code\Added('2026-11-02')]
	public function
	IsLastKey(int|string $Key):
	bool {

		return ($this->GetLastKey() === $Key);
	}

	////////////////////////////////////////////////////////////////

	public function
	IsTrue(int|string $Key):
	bool {

		$Result = $this->Get($Key);

		return ($Result === TRUE);
	}

	public function
	IsTrueEnough(int|string $Key):
	bool {

		$Result = Filter\Type::Bool($this->Get($Key));

		return ($Result === TRUE);
	}

	public function
	IsFalse(int|string $Key):
	bool {

		$Result = $this->Get($Key, FALSE);

		return ($Result === FALSE);
	}

	public function
	IsFalseEnough(int|string $Key):
	bool {

		$Result = Filter\Type::Bool($this->Get($Key, FALSE));

		return ($Result === FALSE);
	}

	public function
	IsNull(int|string $Key):
	bool {

		$Result = $this->Get($Key);

		return ($Result === NULL);
	}

	public function
	IsNullEnough(int|string $Key):
	bool {

		$Result = $this->Get($Key, NULL);

		if(is_string($Result))
		$Result = match(strtoupper(trim($Result))) {
			'NULL', '',
			=> NULL,

			default
			=> $Result
		};

		return ($Result === NULL);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Code\Info('Get how many items are in this dataset.')]
	#[Code\Added('2026-10-27')]
	public function
	Count():
	int {

		return count($this->Dataset);
	}

	#[Code\Info('Check if a key exists in the dataset.')]
	#[Code\Added('2026-10-26')]
	public function
	Has(int|string $Key):
	bool {

		return array_key_exists($Key, $this->Dataset);
	}

	#[Code\Info('Retrieve a key-value if it exists, otherwise return default value.')]
	#[Code\Added('2026-10-26')]
	public function
	Get(int|string $Key, mixed $Default=NULL):
	mixed {

		if(!array_key_exists($Key, $this->Dataset))
		return $Default;

		return $this->RunValueFilter($Key, $this->Dataset[$Key]);
	}

	#[Code\Info('Return a list of key values.')]
	#[Code\Added('2026-11-05')]
	public function
	Pick(int|string ...$Keys):
	static {

		$Output = [];

		foreach($Keys as $Key) {
			$Output[$Key] = $this->Get($Key);
		}

		return new static($Output);
	}

	#[Code\Info('Set a key-value only if the key does not already exist.')]
	#[Code\Added('2026-10-26')]
	public function
	Define(int|string $Key, mixed $Value):
	static {

		if(!$this->Has($Key))
		$this->Set($Key, $Value);

		return $this;
	}

	#[Code\Info('Set a specific key-value.')]
	#[Code\Added('2026-10-26')]
	public function
	Set(int|string $Key, mixed $Value):
	static {

		$this->Dataset[$Key] = $this->RunImportFilter(
			$Key, $Value
		);

		return $this;
	}

	#[Code\Info('Unset a specific key-value.')]
	#[Code\Added('2026-10-28')]
	public function
	Unset(int|string $Key):
	static {

		unset($this->Dataset[$Key]);

		return $this;
	}

	#[Code\Info('Empty the entire dataset.')]
	#[Code\Added('2026-10-31')]
	public function
	Clear():
	static {

		unset($this->Dataset);
		$this->Dataset = [];

		return $this;
	}

	#[Code\Info('Perform a shallow copy of the dataset.')]
	#[Code\Added('2026-10-31')]
	public function
	Copy():
	static {

		$Fresh = new static;

		////////

		$this->EachKeyValue(
			fn(mixed $Key, mixed $Val)
			=> $Fresh->Set($Key, $Val)
		);

		////////

		return $Fresh;
	}

	#[Code\Info('Sort the dataset by its keys.')]
	#[Code\Added('2026-10-31')]
	public function
	Shuffle():
	static {

		shuffle($this->Dataset);

		return $this;
	}

	#[Code\Info('Sort the dataset by its keys.')]
	#[Code\Arg('Func', 'fn(mixed $A, mixed $B): int')]
	#[Code\Added('2026-10-31')]
	public function
	SortByKey(?callable $Func=NULL):
	static {

		if(is_callable($Func))
		uksort($this->Dataset, $Func);
		else
		ksort($this->Dataset);

		return $this;
	}

	#[Code\Info('Sort the dataset by its values.')]
	#[Code\Arg('Func', 'fn(mixed $A, mixed $B): int')]
	#[Code\Added('2026-10-31')]
	public function
	SortByValue(?callable $Func=NULL):
	static {

		if(is_callable($Func))
		uasort($this->Dataset, $Func);
		else
		asort($this->Dataset);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Code\Info('Push a new item onto the end of the dataset.')]
	#[Code\Added('2026-10-27')]
	public function
	Push(mixed $Value):
	static {

		$this->Dataset[] = $Value;

		return $this;
	}

	#[Code\Info('Pop an item off the end of the dataset.')]
	#[Code\Added('2026-10-27')]
	public function
	Pop():
	mixed {

		return array_pop($this->Dataset);
	}

	#[Code\Info('Push a new item onto the start of the dataset.')]
	#[Code\Added('2026-10-27')]
	public function
	PushFirst(mixed $Value):
	static {

		array_unshift($this->Dataset, $Value);

		return $this;
	}

	#[Code\Info('Pop an item off the start of the dataset.')]
	#[Code\Added('2026-10-27')]
	public function
	PopFirst():
	mixed {

		return array_shift($this->Dataset);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Code\Info('Return a list of the keys in this dataset.')]
	#[Code\Added('2026-11-01')]
	public function
	GetKeys():
	static {

		return new static(array_keys($this->Dataset));
	}

	#[Code\Info('Return a list of the values in this dataset.')]
	#[Code\Added('2026-11-02')]
	public function
	GetValues():
	static {

		return new static(array_values($this->Dataset));
	}

	#[Code\Info('Return the first key in this dataset.')]
	#[Code\Added('2026-11-02')]
	public function
	GetFirstKey():
	mixed {

		return array_key_first($this->Dataset);
	}

	#[Code\Info('Return the first value in this dataset.')]
	#[Code\Added('2026-11-02')]
	public function
	GetFirstValue():
	mixed {

		return array_first($this->Dataset);
	}

	#[Code\Info('Return the first key in this dataset.')]
	#[Code\Added('2026-11-02')]
	public function
	GetLastKey():
	mixed {

		return array_key_last($this->Dataset);
	}

	#[Code\Info('Return the last value in this dataset.')]
	#[Code\Added('2026-11-02')]
	public function
	GetLastValue():
	mixed {

		return array_last($this->Dataset);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Code\Info('Append input to end of dataset overwriting existing keys.')]
	#[Code\Added('2026-11-02')]
	public function
	Merge(iterable $Input):
	static {

		if(!is_array($Input))
		$Input = iterator_to_array($Input);

		////////

		$this->ImposeImportFiltersOn($Input);

		$this->Dataset = array_merge(
			$this->Dataset,
			$Input
		);

		return $this;
	}

	#[Code\Info('Prepend input to start of dataset overwriting existing keys.')]
	#[Code\Added('2026-11-02')]
	public function
	MergeStart(iterable $Input):
	static {

		$Pre = [];
		$Key = NULL;
		$Val = NULL;

		////////

		if(!is_array($Input))
		$Input = iterator_to_array($Input);

		////////

		$this->ImposeImportFiltersOn($Input);

		foreach($Input as $Key => $Val) {
			if(!$this->Has($Key)) {
				$Pre[$Key] = $Val;
				continue;
			}

			$this->Dataset[$Key] = $Val;
			continue;
		}

		$this->Dataset = array_merge(
			$Pre,
			$this->Dataset
		);

		////////

		return $this;
	}

	#[Code\Info('Append input to end of dataset without overwriting existing keys.')]
	#[Code\Added('2026-11-01')]
	public function
	Blend(iterable $Input):
	static {

		$Key = NULL;
		$Val = NULL;

		////////

		if(!is_array($Input))
		$Input = iterator_to_array($Input);

		$this->ImposeImportFiltersOn($Input);

		////////

		foreach($Input as $Key => $Val) {
			if($this->Has($Key))
			continue;

			$this->Dataset[$Key] = $Val;
			continue;
		}

		return $this;
	}

	#[Code\Info('Prepend input to start of dataset without overwriting existing keys.')]
	#[Code\Added('2026-11-01')]
	public function
	BlendStart(iterable $Input):
	static {

		$Pre = [];
		$Key = NULL;
		$Val = NULL;

		////////

		if(!is_array($Input))
		$Input = iterator_to_array($Input);

		$this->ImposeImportFiltersOn($Input);

		////////

		foreach($Input as $Key => $Val) {
			if($this->Has($Key))
			continue;

			$Pre[$Key] = $Val;
			continue;
		}

		$this->Dataset = array_merge(
			$Pre,
			$this->Dataset
		);

		////////

		return $this;
	}

	////////////////////////////////////////////////////////////////

	#[Code\Info('Join together as a string using specified delimiter.')]
	#[Code\Added('2026-11-01')]
	public function
	Join(string $Delim):
	string {

		return implode($Delim, $this->Dataset);
	}

	################################################################
	## Data Processing API #########################################
	////////////////////////////////////////////////////////////////

	#[Code\Info('Run specified callable for every item in this dataset.')]
	#[Code\Arg('Func', 'fn(mixed $Value): void')]
	#[Code\Added('2026-10-26')]
	public function
	Each(callable $Func, mixed $Args=NULL):
	static {

		array_walk(
			$this->Dataset,
			fn($V, $K)=> $Func($V, $Args)
		);

		return $this;
	}

	#[Code\Info('Run specified callable for every item in this dataset.')]
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value): void')]
	#[Code\Added('2026-10-26')]
	public function
	EachKeyValue(callable $Func, mixed $Args=NULL):
	static {

		array_walk(
			$this->Dataset,
			fn($V, $K)=> $Func($K, $V, $Args)
		);

		return $this;
	}

	////////////////////////////////////////////////////////////////

	#[Code\Info('Modify this dataset using specified filter callable.')]
	#[Code\Arg('Func', 'fn(mixed $Value): bool $Keep')]
	#[Code\Added('2026-10-27')]
	public function
	Filter(callable $Func):
	static {

		$this->Dataset = array_filter($this->Dataset, $Func);

		return $this;
	}

	#[Code\Info('Modify this dataset using specified filter callable.')]
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value): bool $Keep')]
	#[Code\Added('2026-10-27')]
	public function
	FilterKeyValue(callable $Func):
	static {

		$this->Dataset = array_filter(
			$this->Dataset,
			fn(mixed $Val, int|string $Key)=> $Func($Key, $Val),
			ARRAY_FILTER_USE_BOTH
		);

		return $this;
	}

	////////////////////////////////////////////////////////////////

	#[Code\Info('Distill a new dataset using specified filter callable.')]
	#[Code\Arg('Func', 'fn(mixed $Value): bool $Keep')]
	#[Code\Added('2026-10-27')]
	public function
	Distill(callable $Func):
	static {

		$Result = new static(array_filter($this->Dataset, $Func));

		return $Result;
	}

	#[Code\Info('Distill a new dataset using specified filter callable.')]
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value): bool $Keep')]
	#[Code\Added('2026-10-27')]
	public function
	DistillKeyValue(callable $Func):
	static {

		$Result = new static(array_filter(
			$this->Dataset,
			fn(mixed $Val, int|string $Key)=> $Func($Key, $Val),
			ARRAY_FILTER_USE_BOTH
		));

		return $Result;
	}

	////////////////////////////////////////////////////////////////

	#[Code\Info('Map a new dataset mapped using specified callable.')]
	#[Code\Arg('Func', 'fn(mixed $Value): mixed')]
	#[Code\Added('2026-10-28')]
	public function
	Map(callable $Func):
	static {

		$Result = new static(array_map(
			$Func,
			$this->Dataset
		));

		return $Result;
	}

	#[Code\Info('Map a new dataset mapped using specified callable.')]
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value): mixed')]
	#[Code\Added('2026-10-28')]
	public function
	MapKeyValue(callable $Func):
	static {

		$Result = new Datastore;

		////////

		foreach($this->Dataset as $K => $V)
		$Result->Set($K, $Func($K, $V));

		////////

		return $Result;
	}

	////////////////////////////////////////////////////////////////

	#[Code\Info('Remap this dataset using specified callable.')]
	#[Code\Arg('Func', 'fn(mixed $Value): mixed')]
	#[Code\Added('2026-10-28')]
	public function
	Remap(callable $Func):
	static {

		$this->Dataset = array_map(
			$Func,
			$this->Dataset
		);

		return $this;
	}

	#[Code\Info('Remap this dataset using specified callable.')]
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value): mixed')]
	#[Code\Added('2026-10-28')]
	public function
	RemapKeyValue(callable $Func):
	static {

		foreach($this->Dataset as $K => $V)
		$this->Set($K, $Func($K, $V));

		////////

		return $this;
	}

	////////////////////////////////////////////////////////////////

	#[Code\Info('Reindex dataset into cleanly numerically indexed list.')]
	#[Code\Added('2026-10-31')]
	public function
	Reindex():
	static {

		$this->Dataset = array_values($this->Dataset);

		return $this;
	}

	#[Code\Info('Reindex this dataset using the specified callable.')]
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value): NULL', 'Leave key and value unchanged.')]
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value): FALSE', 'Remove key and data from set.')]
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value): int|string', 'Move existing data to specified key.')]
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value): array<k,v>', 'Move data to the specified key with the specified data.')]
	#[Code\Added('2026-10-31')]
	public function
	ReindexKeyValue(callable $Func):
	static {

		$Map = $this->MapKeyValue($Func);
		$Set = [];
		$Key = NULL;
		$Val = NULL;

		////////

		foreach($Map as $Key => $Val) {

			// if the callable returns a boolean false then drop the
			// field/value from the set.

			if($Val === FALSE) {
				continue;
			}

			// if the callable returns a null then keep this field as it
			// already was.

			if($Val === NULL) {
				$Set[$Key] = $this->Get($Key);
				continue;
			}

			// if the callable returns a string or int then rewrite
			// this field to the new key name.

			if(is_string($Val) || is_int($Val)) {
				$Set[$Val] = $this->Get($Key);
				continue;
			}

			// if the callable returns an array key-value pair then
			// rewrite this field to the new key name and with the
			// new value.

			if(is_array($Val) && (count($Val) === 2))
			if(is_int($Val[0]) || is_string($Val[0])) {
				$Set[$Val[0]] = $Val[1];
				continue;
			}
			
			// otherwise, explode.

			throw new Datastore\Error\ReindexFormatInvalid($Val);
		}

		$this->Import($Set);

		////////

		return $this;
	}

	////////////////////////////////////////////////////////////////

	#[Code\Info('Accumulate value by iterating over each item with the specified callable.')]
	#[Code\Arg('Func', 'fn(mixed $Value, mixed $Carry)')]
	#[Code\Added('2026-11-03')]
	public function
	Roll(callable $Func, mixed $Initial=0):
	mixed {
	
		$Accum = $Initial;
		$Val = NULL;

		foreach($this->Dataset as $Val) 
		$Accum = $Func($Val, $Accum);

		return $Accum;
	}

	#[Code\Info('Accumulate value by iterating over each item with the specified callable.')]
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value, mixed $Carry)')]
	#[Code\Added('2026-11-03')]
	public function
	RollKeyValue(callable $Func, mixed $Initial=0):
	mixed {
	
		$Accum = $Initial;
		$Key = NULL;
		$Val = NULL;

		foreach($this->Dataset as $Key => $Val) 
		$Accum = $Func($Key, $Val, $Accum);

		return $Accum;
	}

	################################################################
	## Factory API #################################################
	////////////////////////////////////////////////////////////////

	#[Code\Added('2026-10-26')]
	#[Code\Info('Factory style constructor.')]
	static public function
	New(iterable $Input=[]):
	static {

		$Store = new static($Input);

		return $Store;
	}

	#[Code\Added('2026-11-01')]
	#[Code\Info('Factory to create datastore by string explosion.')]
	static public function
	FromString(string $Input, string $Delim=''):
	static {

		$Bits = match(TRUE){
			($Delim === '')
			=> mb_str_split($Input, 1),

			default
			=> explode($Delim, $Input)
		};

		$Store = new static($Bits);

		return $Store;
	}

	#[Code\Added('2026-11-05')]
	#[Code\Info('Factory to create datastore merging stacks.')]
	static public function
	FromStackBlend(iterable $Input, array ...$MoreIterables):
	static {

		$Output = new Datastore($Input);
		$Iterable = NULL;

		////////

		foreach($MoreIterables as $Iterable) {
			if(is_iterable($Iterable))
			$Output->Blend($Iterable);

			continue;
		}

		////////

		return $Output;
	}

	#[Code\Added('2026-11-05')]
	#[Code\Info('Factory to create datastore merging stacks.')]
	static public function
	FromStackMerge(iterable $Input, array ...$MoreIterables):
	static {

		$Output = new Datastore($Input);
		$Iterable = NULL;

		////////

		foreach($MoreIterables as $Iterable) {
			if(is_iterable($Iterable))
			$Output->Merge($Iterable);

			continue;
		}

		////////

		return $Output;
	}

	################################################################
	## PHP Standard Library Interfaces #############################
	////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////
	// interface: ArrayAccess //////////////////////////////////////

	#[Code\Internal]
	#[Code\Added('2026-10-28')]
	public function
	OffsetExists(mixed $Key):
	bool {

		return $this->Has($Key);
	}

	#[Code\Internal]
	#[Code\Added('2026-10-28')]
	public function
	OffsetGet(mixed $Key):
	mixed {

		return $this->Get($Key);
	}

	#[Code\Internal]
	#[Code\Added('2026-10-28')]
	public function
	OffsetSet(mixed $Key, mixed $Val):
	void {

		$this->Set($Key, $Val);
		return;
	}

	#[Code\Internal]
	#[Code\Added('2026-10-28')]
	public function
	OffsetUnset(mixed $Key):
	void {

		$this->Unset($Key);
		return;
	}

	////////////////////////////////////////////////////////////////
	// interface: Iterator /////////////////////////////////////////

	#[Code\Internal]
	#[Code\Added('2026-10-28')]
	public function
	Current():
	mixed {

		$Key = $this->Key();

		return $this->Get($Key);
	}

	#[Code\Internal]
	#[Code\Added('2026-10-28')]
	public function
	Key():
	mixed {

		return key($this->Dataset);
	}

	#[Code\Internal]
	#[Code\Added('2026-10-28')]
	public function
	Next():
	void {

		next($this->Dataset);
		return;
	}

	#[Code\Internal]
	#[Code\Added('2026-10-28')]
	public function
	Rewind():
	void {

		reset($this->Dataset);
		return;
	}

	#[Code\Internal]
	#[Code\Added('2026-10-28')]
	public function
	Valid():
	bool {

		return (key($this->Dataset) !== NULL);
	}

};
