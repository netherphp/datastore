<?php ##########################################################################
################################################################################

namespace Nether\Meta\Sensei;

use ReflectionParameter;

################################################################################
################################################################################

class FunctionReturnDoc {

	public string
	$Type;

	public function
	__Construct(mixed $Arg) {
	
		if($Arg === NULL)
		$this->Type = 'mixed';
		else
		$this->Type = (string)$Arg;

		return;
	}

};
