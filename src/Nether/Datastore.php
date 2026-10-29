<?php ##########################################################################
################################################################################

namespace Nether;

use ArrayAccess;
use Iterator;
use Countable;
use JsonSerializable;
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
	__construct(iterable $Input=[]) {

		$this->Import($Input);

		return;
	}

	################################################################
	################################################################

	#[Code\Info('Return the internal dataset array.')]
	#[Code\Added('2026-10-26')]
	public function
	Export():
	array {

		return $this->Dataset;
	}

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

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Code\Info('Check if this dataset is a "list" meaning cleanly numerically indexed.')]
	#[Code\Added('2026-10-27')]
	public function
	IsList():
	bool {

		return array_is_list($this->Dataset);
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

	#[Code\Info('Turn this dataset into a cleanly numerically indexed list.')]
	#[Code\Added('2026-10-27')]
	public function
	Listify():
	static {

		$this->Dataset = array_values($this->Dataset);

		return $this;
	}

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
	#[Code\Arg('Func', 'fn(mixed $Value)')]
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
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value)')]
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
	#[Code\Arg('Func', 'fn(mixed $Value)')]
	#[Code\Added('2026-10-27')]
	public function
	Filter(callable $Func):
	static {

		$this->Dataset = array_filter($this->Dataset, $Func);

		return $this;
	}

	#[Code\Info('Modify this dataset using specified filter callable.')]
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value)')]
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
	#[Code\Arg('Func', 'fn(mixed $Value)')]
	#[Code\Added('2026-10-27')]
	public function
	Distill(callable $Func):
	static {

		$Result = new static(array_filter($this->Dataset, $Func));

		return $Result;
	}

	#[Code\Info('Distill a new dataset using specified filter callable.')]
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value)')]
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
	#[Code\Arg('Func', 'fn(mixed $Value)')]
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
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value)')]
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
	#[Code\Arg('Func', 'fn(mixed $Value)')]
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
	#[Code\Arg('Func', 'fn(mixed $Key, mixed $Value)')]
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
	////////////////////////////////////////////////////////////////

	#[Code\Added('2026-10-26')]
	static public function
	New(iterable $Input=[]):
	static {

		$Store = new static($Input);

		return $Store;
	}

	################################################################
	## interface: ArrayAccess ######################################

	/** @internal */
	#[Code\Added('2026-10-28')]
	public function
	OffsetExists(mixed $Key):
	bool {

		return $this->Has($Key);
	}

	/** @internal */
	#[Code\Added('2026-10-28')]
	public function
	OffsetGet(mixed $Key):
	mixed {

		return $this->Get($Key);
	}

	/** @internal */
	#[Code\Added('2026-10-28')]
	public function
	OffsetSet(mixed $Key, mixed $Val):
	void {

		$this->Set($Key, $Val);
		return;
	}

	/** @internal */
	#[Code\Added('2026-10-28')]
	public function
	OffsetUnset(mixed $Key):
	void {

		$this->Unset($Key);
		return;
	}

	################################################################
	## interface: Countable ########################################

	#[Code\Info('Get how many items are in this dataset.')]
	#[Code\Added('2026-10-27')]
	public function
	Count():
	int {

		return count($this->Dataset);
	}

	################################################################
	## interface: Iterator #########################################

	/** @internal */
	#[Code\Added('2026-10-28')]
	public function
	Current():
	mixed {

		return current($this->Dataset);
	}

	/** @internal */
	#[Code\Added('2026-10-28')]
	public function
	Key():
	mixed {

		return key($this->Dataset);
	}

	/** @internal */
	#[Code\Added('2026-10-28')]
	public function
	Next():
	void {

		next($this->Dataset);
		return;
	}

	/** @internal */
	#[Code\Added('2026-10-28')]
	public function
	Rewind():
	void {

		reset($this->Dataset);
		return;
	}

	/** @internal */
	#[Code\Added('2026-10-28')]
	public function
	Valid():
	bool {

		return (key($this->Dataset) !== NULL);
	}

};

