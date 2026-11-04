<?php ##########################################################################
################################################################################

namespace Nether\Datastore;

use Nether\Meta\Code;

################################################################################
################################################################################

trait DatasetFilterPackage {

	protected array
	$Filters = [];

	public function
	SetFilter(string $Key, array|callable $Stack):
	static {

		if(!is_array($Stack))
		$Stack = [ $Stack ];

		$this->Filters[$Key] = $Stack;

		return $this;
	}

	public function
	RunFilter(string $Key, mixed $Input):
	mixed {

		$Output = $Input;
		$FList = NULL;

		////////

		if(!array_key_exists($Key, $this->Filters))
		return $Output;

		////////

		foreach($this->Filters[$Key] as $Fn) {
			$Output = $Fn($Output);
			continue;
		}

		return $Output;
	}

};
