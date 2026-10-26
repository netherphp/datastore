<?php ##########################################################################
################################################################################

namespace Nether;

use Exception;

################################################################################
################################################################################

class Datastore {

	protected array
	$Dataset;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(iterable $Input=[]) {

		$this->SetData($Input);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetData():
	array {

		return $this->Dataset;
	}

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
	// api that does not change data ///////////////////////////////

	public function
	Each(callable $Func, mixed $Args=NULL):
	static {

		array_walk(
			$this->Dataset,
			fn($V, $K)=> $Func($V, $Args)
		);

		return $this;
	}

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
	// api that does change data ///////////////////////////////////

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	New(iterable $Input=[]):
	static {

		$Store = new static($Input);

		return $Store;
	}

};

