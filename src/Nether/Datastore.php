<?php ##########################################################################
################################################################################

namespace Nether;

use Nether\Meta;

use Exception;

################################################################################
################################################################################

class Datastore {

	#[Meta\Info('Internal dataset.')]
	#[Meta\Date('2026-10-26')]
	protected array
	$Dataset;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Meta\Date('2026-10-26')]
	public function
	__Construct(iterable $Input=[]) {

		$this->SetData($Input);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Meta\Info('Return the internal dataset array.')]
	#[Meta\Date('2026-10-26')]
	public function
	GetData():
	array {

		return $this->Dataset;
	}

	#[Meta\Info('Populate the internal dataset with the specified data.')]
	#[Meta\Date('2026-10-26')]
	public function
	SetData(iterable $Input):
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

	#[Meta\Info('Check if this dataset is a "list" meaning cleanly numerically indexed.')]
	#[Meta\Date('2026-10-27')]
	public function
	IsList():
	bool {

		return array_is_list($this->Dataset);
	}

	#[Meta\Info('Get how many items are in this dataset.')]
	#[Meta\Date('2026-10-27')]
	public function
	Count():
	int {

		return count($this->Dataset);
	}

	#[Meta\Info('Check if a key exists in the dataset.')]
	#[Meta\Date('2026-10-26')]
	public function
	Has(int|string $Key):
	bool {

		return array_key_exists($Key, $this->Dataset);
	}

	#[Meta\Info('Retrieve a key-value if it exists, otherwise return default value.')]
	#[Meta\Date('2026-10-26')]
	public function
	Get(int|string $Key, mixed $Default=NULL):
	mixed {

		if(!$this->Has($Key))
		return $Default;

		return $this->Dataset[$Key];
	}

	#[Meta\Info('Set a key-value only if the key does not already exist.')]
	#[Meta\Date('2026-10-26')]
	public function
	Define(int|string $Key, mixed $Value):
	static {

		if(!$this->Has($Key))
		$this->Set($Key, $Value);

		return $this;
	}

	#[Meta\Info('Set a specific key-value.')]
	#[Meta\Date('2026-10-26')]
	public function
	Set(int|string $Key, mixed $Value):
	static {

		$this->Dataset[$Key] = $Value;

		return $this;
	}

	#[Meta\Info('Turn this dataset into a cleanly numerically indexed list.')]
	#[Meta\Date('2026-10-27')]
	public function
	Listify():
	static {

		$this->Dataset = array_values($this->Dataset);

		return $this;
	}

	#[Meta\Info('Push a new item onto the end of the dataset.')]
	#[Meta\Date('2026-10-27')]
	public function
	Push(mixed $Value):
	static {

		$this->Dataset[] = $Value;

		return $this;
	}

	#[Meta\Info('Pop an item off the end of the dataset.')]
	#[Meta\Date('2026-10-27')]
	public function
	Pop():
	mixed {

		return array_pop($this->Dataset);
	}

	#[Meta\Info('Push a new item onto the start of the dataset.')]
	#[Meta\Date('2026-10-27')]
	public function
	PushFirst(mixed $Value):
	static {

		array_unshift($this->Dataset, $Value);

		return $this;
	}

	#[Meta\Info('Pop an item off the start of the dataset.')]
	#[Meta\Date('2026-10-27')]
	public function
	PopFirst():
	mixed {

		return array_shift($this->Dataset);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Meta\Info('Run fn(mixed $Value) on every item in this dataset.')]
	#[Meta\Date('2026-10-26')]
	public function
	Each(callable $Func, mixed $Args=NULL):
	static {

		array_walk(
			$this->Dataset,
			fn($V, $K)=> $Func($V, $Args)
		);

		return $this;
	}

	#[Meta\Info('Run fn(mixed $Key, mixed $Value) on every item in this dataset.')]
	#[Meta\Date('2026-10-26')]
	public function
	EachKeyValue(callable $Func, mixed $Args=NULL):
	static {

		array_walk(
			$this->Dataset,
			fn($V, $K)=> $Func($K, $V, $Args)
		);

		return $this;
	}

	#[Meta\Info('Filter dataset using the supplied callable($Val).')]
	#[Meta\Date('2026-10-27')]
	public function
	Filter(callable $FuncVal):
	static {

		$this->Dataset = array_filter($this->Dataset, $FuncVal);

		return $this;
	}

	#[Meta\Info('Filter dataset using the supplied callable($Key, $Val).')]
	#[Meta\Date('2026-10-27')]
	public function
	FilterKeyValue(callable $FuncKeyVal):
	static {

		$this->Dataset = array_filter(
			$this->Dataset,
			fn(mixed $Val, int|string $Key)=> $FuncKeyVal($Key, $Val),
			ARRAY_FILTER_USE_BOTH
		);

		return $this;
	}

	#[Meta\Info('Filter into new Datastore object using the supplied callable($Val).')]
	#[Meta\Date('2026-10-27')]
	public function
	Distill(callable $FuncVal):
	Datastore {

		$Result = new static(array_filter($this->Dataset, $FuncVal));

		return $Result;
	}

	#[Meta\Info('Filter into new Datastore object using the supplied callable($Key, $Val).')]
	#[Meta\Date('2026-10-27')]
	public function
	DistillKeyValue(callable $FuncKeyVal):
	Datastore {

		$Result = new static(array_filter(
			$this->Dataset,
			fn(mixed $Val, int|string $Key)=> $FuncKeyVal($Key, $Val),
			ARRAY_FILTER_USE_BOTH
		));

		return $Result;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Meta\Date('2026-10-26')]
	static public function
	New(iterable $Input=[]):
	static {

		$Store = new static($Input);

		return $Store;
	}

};

