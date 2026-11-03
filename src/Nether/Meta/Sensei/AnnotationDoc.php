<?php ##########################################################################
################################################################################

namespace Nether\Meta\Sensei;

use ReflectionAttribute;

################################################################################
################################################################################

class AnnotationDoc {

	public ClassDoc
	$Class;

	public ?object
	$Object;

	public function
	__Construct(string $Class, ?object $Instance=NULL) {

		$this->Class = new ClassDoc($Class);
		$this->Object = $Instance;

		return;
	}

	static public function
	FromReflection(ReflectionAttribute $Attr):
	static {

		$Output = new static(
			$Attr->getName(),
			$Attr->newInstance()
		);

		return $Output;
	}

};
