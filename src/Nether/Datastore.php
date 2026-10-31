<?php ##########################################################################
################################################################################

namespace Nether;

use ArrayAccess;
use Iterator;
use Countable;
use JsonSerializable;
use Nether\Datastore\Error\FileAccessUnwritable;
use Nether\Meta\Code;

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

	#[Code\Added('2026-10-26')]
	public function
	__Construct(iterable $Input=[]) {

		$this->Import($Input);

		return;
	}

	################################################################
	################################################################

	#[Code\Info('Populate the internal dataset with the specified data.')]
	#[Code\Added('2026-10-26')]
	public function
	Import(iterable $Input):
	static {

		$this->Dataset = match(TRUE) {
			(!is_array($Input))
			=> [ ... $Input ],

			default
			=> $Input
		};

		reset($this->Dataset);

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

		$Data = unserialize(file_get_contents($Filename));

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
			throw new FileAccessUnwritable(dirname($Filename));
		}

		elseif(!is_writable($Filename))
		throw new FileAccessUnwritable($Filename);

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

		if(!$this->Has($Key))
		return $Default;

		return $this->Dataset[$Key];
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

		$this->Dataset[$Key] = $Value;

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

		$this->Import([]);

		return $this;
	}

	#[Code\Info('Perform a shallow copy of the dataset.')]
	#[Code\Added('2026-10-31')]
	public function
	Copy():
	static {

		$Fresh = new static($this->Export());

		return $Fresh;
	}

	#[Code\Info('Sort the dataset by its keys.')]
	#[Code\Arg('Func', 'fn(mixed $A, mixed $B): int')]
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
	#[Code\Arg('Func', 'fn(mixed $Value): mixed $ValueUpdate')]
	#[Code\Added('2026-10-28')]
	public function
	Map(callable $FnVal):
	static {

		$Result = new static(array_map(
			$FnVal,
			$this->Dataset
		));

		return $Result;
	}

	#[Code\Info('Map a new dataset mapped using specified callable.')]
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value): mixed $ValueUpdate')]
	#[Code\Added('2026-10-28')]
	public function
	MapKeyValue(callable $FnKeyVal):
	static {

		$Result = new Datastore;

		////////

		foreach($this->Dataset as $K => $V)
		$Result->Set($K, $FnKeyVal($K, $V));

		////////

		return $Result;
	}

	////////////////////////////////////////////////////////////////

	#[Code\Info('Remap this dataset using specified callable.')]
	#[Code\Arg('Func', 'fn(mixed $Value): mixed $ValueUpdate')]
	#[Code\Added('2026-10-28')]
	public function
	Remap(callable $FnKey):
	static {

		$this->Dataset = array_map(
			$FnKey,
			$this->Dataset
		);

		return $this;
	}

	#[Code\Info('Remap this dataset using specified callable.')]
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value): mixed $ValueUpdate')]
	#[Code\Added('2026-10-28')]
	public function
	RemapKeyValue(callable $FnKeyVal):
	static {

		foreach($this->Dataset as $K => $V)
		$this->Set($K, $FnKeyVal($K, $V));

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
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value): int|string|array<k,v>|NULL|FALSE')]
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
	////////////////////////////////////////////////////////////////

	#[Code\Added('2026-10-26')]
	#[Code\Info('Factory style constructor.')]
	static public function
	New(iterable $Input=[]):
	static {

		$Store = new static($Input);

		return $Store;
	}

	################################################################
	## interface: ArrayAccess ######################################

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

	################################################################
	## interface: Iterator #########################################

	#[Code\Internal]
	#[Code\Added('2026-10-28')]
	public function
	Current():
	mixed {

		return current($this->Dataset);
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
