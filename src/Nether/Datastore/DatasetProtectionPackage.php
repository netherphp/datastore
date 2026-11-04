<?php ##########################################################################
################################################################################

namespace Nether\Datastore;

use Nether\Meta\Code;

################################################################################
################################################################################

trait DatasetProtectionPackage {

	#[Code\Info('List of keys to omit during debug dumps.')]
	#[Code\Added('2026-11-03')]
	protected array
	$ProtectedKeys = [];

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Code\Info('Overwriting the default PHP __debugInfo.')]
	#[Code\Added('2026-11-03')]
	public function
	__DebugInfo():
	array {

		$Key = NULL;
		$Val = NULL;
		$Output = [];
		$Data = [];

		////////

		foreach($this->Dataset as $Key => $Val) {
			if(array_key_exists($Key, $this->ProtectedKeys)) {
				if($this->ProtectedKeys[$Key] === FALSE)
				continue;
			
				$Data[$Key] = sprintf('[protected %s]', gettype($Val));
				continue;
			}

			$Data[$Key] = $Val;
			continue;
		}

		////////

		$Output['Dataset'] = $Data;

		return $Output;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	AddProtectedKey(int|string $Key, bool $Strip=FALSE):
	static {

		$this->ProtectedKeys[$Key] = !$Strip;

		return $this;
	}

	public function
	RemoveProtectedKey(int|string $Key):
	static {

		unset($this->ProtectedKeys[$Key]);
		
		return $this;
	}

	public function
	ClearProtectedKeys():
	static {

		unset($this->ProtectedKeys);
		$this->ProtectedKeys = [];

		return $this;
	}

};
