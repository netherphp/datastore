<?php ##########################################################################
################################################################################

$AppRoot = dirname(__FILE__, 2);
$Outfile = sprintf('%s/docs/api.md', $AppRoot);

require(sprintf('%s/vendor/autoload.php', $AppRoot));

use Nether\Meta\Sensei\ClassMethodDoc;

################################################################################
################################################################################

function
FilterUserlandMethods(ClassMethodDoc $Method):
bool {
	return (TRUE
		&& ($Method->Access === 'public')
		&& !($Method->HasAnnotation(Nether\Meta\Code\Internal::class))
	);
};

function
SortUserlandMethods(ClassMethodDoc $A, ClassMethodDoc $B):
int {

	if($A->Static && !$B->Static)
	return 1;

	if($B->Static && !$A->Static)
	return -1;

	return $A->Name <=> $B->Name;
};

function
PrintMethodSignature(ClassMethodDoc $Method):
void {

	printf("## `%s`\n", $Method->Name);

	printf(
		"```php\n%s( %s ): %s;\n```\n",
		match(TRUE) {
			($Method->Constructor)
			=> 'new Datastore',

			($Method->Static)
			=> "Datastore::{$Method->Name}",

			default
			=> "\$Data->{$Method->Name}"
		},
		$Method->GetParameterSignature(),
		$Method->GetReturnSignature()
	);

	return;
};

function
PrintMethodInfo(ClassMethodDoc $Method):
void {

	echo "\n";
	echo "{$Method->GetInfo()}\n";
	echo "\n";

	return;
};

function
PrintMethodArgInfo(ClassMethodDoc $Method):
void {

	$ArgInfo = (
		($Method->Annotations)
		->Distill(
			fn(Nether\Meta\Sensei\AnnotationDoc $A)
			=> $A->Object instanceof Nether\Meta\Code\Arg
		)
		->Map(
			fn(Nether\Meta\Sensei\AnnotationDoc $A)
			=> $A->Object
		)
	);

	if($ArgInfo->Count()) {
		$ArgInfo->Each(function(Nether\Meta\Code\Arg $Arg) {

			printf("> `%s`: `%s`\n", $Arg->Name, $Arg->Type);

			if($Arg->Info)
			printf("\n%s\n", $Arg->Info);
			else
			print("\n");
		});

		echo "\n";
	}

	return;
};

////////////////////////////////////////////////////////////////////////////////

$Doc = new Nether\Meta\Sensei\ClassDoc(Nether\Datastore::class);
$Buffer = NULL;

ob_start();

////////////////////////////////////////////////////////////////////////////////

($Doc->Methods)
->Distill(FilterUserlandMethods(...))
->SortByValue(SortUserlandMethods(...))
->Each(function(Nether\Meta\Sensei\ClassMethodDoc $Method) {
	PrintMethodSignature($Method);
	PrintMethodInfo($Method);
	PrintMethodArgInfo($Method);
	echo "\n";
	return;
});

////////////////////////////////////////////////////////////////////////////////

$Buffer = ob_get_clean();
file_put_contents($Outfile, $Buffer);

echo $Buffer;

////////////////////////////////////////////////////////////////////////////////
