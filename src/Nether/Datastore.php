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

	#[Meta\Info('Retrieve a specific value if it exists, otherwise return default value.')]
	#[Meta\Date('2026-10-26')]
	public function
	Has(int|string $Key):
	bool {

		return array_key_exists($Key, $this->Dataset);
	}

	#[Meta\Info('Retrieve a specific value if it exists, otherwise return default value.')]
	#[Meta\Date('2026-10-26')]
	public function
	Get(int|string $Key, mixed $Default=NULL):
	mixed {

		if(!array_key_exists($Key, $this->Dataset))
		return $Default;

		return $this->Dataset[$Key];
	}

	#[Meta\Info('Set a specific value only if the key does not already exist.')]
	#[Meta\Date('2026-10-26')]
	public function
	Define(int|string $Key, mixed $Value):
	static {

		if(!$this->Has($Key))
		$this->Set($Key, $Value);

		return $this;
	}

	#[Meta\Info('Set a specific value.')]
	#[Meta\Date('2026-10-26')]
	public function
	Set(int|string $Key, mixed $Value):
	static {

		$this->Dataset[$Key] = $Value;

		return $this;
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

