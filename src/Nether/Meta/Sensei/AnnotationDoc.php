<?php ##########################################################################
################################################################################

namespace Nether\Meta\Sensei;

use ReflectionAttribute;

################################################################################
################################################################################

class AnnotationDoc {

	public string
	$Namespace;

	public string
	$Name;

	public object
	$Object;

	public function
	__Construct(ReflectionAttribute $Attribute) {

		$Class = new ClassDoc($Attribute->getName());
		$this->Namespace = $Class->Namespace;
		$this->Name = $Class->Name;
		$this->Object = $Attribute->newInstance();

		return;
	}

};
