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
	__Construct(string $Name, string $Type='mixed') {

		$this->Name = $Name;
		$this->Type = $Type;

		return;
	}

	static public function
	FromReflection(ReflectionParameter $Ref):
	static {

		$RefType = $Ref->getType();
		$StrName = $Ref->getName();
		$StrType = 'mixed';

		if($RefType)
		$Type = (string)$RefType;

		$Output = new static($StrName, $StrType);

		return $Output;
	}

};
