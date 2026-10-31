<?php ##########################################################################
################################################################################

namespace Nether\Meta\Sensei;

use ReflectionParameter;

################################################################################
################################################################################

class FunctionArgDoc {

	public string
	$Name;

	public string
	$Type;

	public function
	__Construct(ReflectionParameter $Arg) {

		$this->Name = $Arg->getName();

		$Type = $Arg->getType();
		
		if($Type === NULL)
		$this->Type = 'mixed';
		else
		$this->Type = (string)$Type;

		return;
	}

};
