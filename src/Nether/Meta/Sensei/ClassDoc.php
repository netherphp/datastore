<?php ##########################################################################
################################################################################

namespace Nether\Meta\Sensei;

use ReflectionClass;
use Nether\Datastore;
use PHPUnit\Event\Code\ClassMethod;
use ReflectionMethod;

################################################################################
################################################################################

class ClassDoc {

	public string
	$Namespace;

	public string
	$Name;

	public Datastore
	$Methods;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(string $Class) {

		$this->PopulateClassInfo($Class);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	PopulateClassInfo(string $Class):
	void {

		$Info = new ReflectionClass($Class);
		$this->Namespace = $Info->getNamespaceName();
		$this->Name = $Info->getShortName();

		$Methods = new Datastore($Info->getMethods());
		$Methods->Remap(
			fn(ReflectionMethod $Func)
			=> new ClassMethodDoc(
				$Func->getDeclaringClass()->getName(),
				$Func->getName()
			)
		);

		$this->Methods = $Methods;

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetFQCN():
	string {

		return sprintf('%s\\%s', $this->Namespace, $this->Name);
	}

};
