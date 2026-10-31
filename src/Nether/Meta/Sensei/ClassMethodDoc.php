<?php ##########################################################################
################################################################################

namespace Nether\Meta\Sensei;

use ReflectionMethod;
use ReflectionAttribute;
use ReflectionParameter;
use Nether\Datastore;
use Nether\Meta\Code;

################################################################################
################################################################################

class ClassMethodDoc {

	public string
	$Namespace;

	public string
	$Class;

	public string
	$Name;

	public bool
	$Static;

	public string
	$Access;

	public string
	$ReturnType;

	public bool
	$Constructor;

	public Datastore
	$Annotations;

	public Datastore
	$Parameters;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(string $Class, string $Method) {

		$this->PopulateMethodInfo($Class, $Method);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	PopulateMethodInfo(string $Class, string $Method):
	void {

		$Info = new ReflectionMethod($Class, $Method);
		$Class = $Info->getDeclaringClass();
		$Annos = new Datastore($Info->getAttributes());
		$Params = new Datastore($Info->getParameters());
		$Return = $Info->getReturnType();

		$Annos->Remap(function(ReflectionAttribute $Attrib) {
			return new AnnotationDoc($Attrib);
		});

		$Params->Remap(function(ReflectionParameter $Arg) {
			return new FunctionArgDoc($Arg);
		});

		$this->Namespace = $Class->getNamespaceName();
		$this->Class = $Class->getShortName();
		$this->Name = $Info->getShortName();
		$this->Static = $Info->isStatic();
		$this->Annotations = $Annos;
		$this->Parameters = $Params;

		$this->Access = match(TRUE) {
			($Info->isPublic()) => 'public',
			($Info->isProtected()) => 'protected',
			($Info->isPrivate()) => 'private'
		};

		$this->ReturnType = new FunctionReturnDoc($Return)->Type;

		if(strtolower($this->Name) === '__construct')
		$this->Constructor = TRUE;
		else
		$this->Constructor = FALSE;

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	HasAnnotation(string $Class):
	bool {

		$Has = FALSE;

		($this->Annotations)
		->Each(function(AnnotationDoc $A) use($Class, &$Has) {

			if($A->Object instanceof $Class)
			$Has = TRUE;

			return;
		});

		return $Has;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetParameterSignature():
	string {

		$Output = [ ];

		foreach($this->Parameters as $P) {
			$Output[] = "{$P->Type} {$P->Name}";
		}

		if(!count($Output))
		return 'void';

		return join(', ', $Output);
	}
	
	public function
	GetAccessSignature():
	string {

		$Output = [];

		if($this->Static)
		$Output[] = 'static';

		$Output[] = $this->Access;

		return join(' ', $Output);
	}

	public function
	GetReturnSignature():
	string {

		if($this->Constructor)
		return 'void';

		return $this->ReturnType;
	}

	public function
	GetInfo():
	string {

		$Info = $this->Annotations->Distill(
			fn(AnnotationDoc $A)=> $A->Object::class === Code\Info::class
		);
		
		if($Info->IsEmpty())
		return 'No info provided.';

		return $Info->Current()->Object->Message;
	}

};
