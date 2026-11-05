<?php ##########################################################################
################################################################################

namespace Nether\Datastore;

use Nether\Meta\Code;

################################################################################
################################################################################

trait DatasetFilterPackage {

	////////////////////////////////////////////////////////////////
	// Value Filter API: ///////////////////////////////////////////

	// value filters get run when data is read from the datastore, values are
	// still stored in their original unaltered form.

	protected array
	$ValueFilters = [];

	public function
	SetValueFilter(int|string $Key, array|callable $Stack):
	static {

		if(!is_array($Stack))
		$Stack = [ $Stack ];

		$this->ValueFilters[$Key] = $Stack;

		return $this;
	}

	public function
	UnsetValueFilter(int|string $Key):
	static {

		unset($this->ValueFilters[$Key]);

		return $this;
	}

	protected function
	RunValueFilter(int|string $Key, mixed $Input):
	mixed {

		if(!array_key_exists($Key, $this->ValueFilters))
		return $Input;

		////////

		$Output = $Input;

		foreach($this->ValueFilters[$Key] as $Fn) {
			$Output = $Fn($Output);
			continue;
		}

		return $Output;
	}

	////////////////////////////////////////////////////////////////
	// Import Filter API: //////////////////////////////////////////

	// import filters get run when data is pushed into the datastore. values
	// are stored as their filtered result, the original data will be lost.

	protected array
	$ImportFilters = [];

	public function
	SetImportFilter(int|string $Key, array|callable $Stack):
	static {

		if(!is_array($Stack))
		$Stack = [ $Stack ];

		$this->ImportFilters[$Key] = $Stack;

		return $this;
	}

	public function
	UnsetImportFilter(int|string $Key):
	static {

		unset($this->ImportFilters[$Key]);

		return $this;
	}

	protected function
	RunImportFilter(int|string $Key, mixed $Input):
	mixed {

		if(!array_key_exists($Key, $this->ImportFilters))
		return $Input;

		////////

		$Output = $Input;

		foreach($this->ImportFilters[$Key] as $Fn) {
			$Output = $Fn($Output);
			continue;
		}

		return $Output;
	}

	protected function
	ImposeImportFiltersOn(array &$Data):
	void {

		foreach($this->ImportFilters as $Key => $Stack) {
			if(!array_key_exists($Key, $Data))
			continue;

			$Data[$Key] = $this->RunImportFilter($Key, $Data[$Key]);
			continue;
		}

		return;
	}

};
