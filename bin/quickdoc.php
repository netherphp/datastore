<?php ##########################################################################
################################################################################

$AppRoot = dirname(__FILE__, 2);
$Outfile = sprintf('%s/docs/api.md', $AppRoot);

require(sprintf('%s/vendor/autoload.php', $AppRoot));

################################################################################
################################################################################

// atm the only real purpose of this is to quickly dump formatted text to
// paste into the readme.

$Doc = new Nether\Meta\Sensei\ClassDoc(Nether\Datastore::class);
$Buffer = NULL;-

////////////////////////////////////////////////////////////////////////////////

ob_start();

////////////////////////////////////////////////////////////////////////////////

echo "## Class: `{$Doc->GetFQCN()}`\n\n";

($Doc->Methods)
->Distill(
	fn(Nether\Meta\Sensei\ClassMethodDoc $Method)
	=> (
		($Method->Access === 'public')
		&& !$Method->HasAnnotation(Nether\Meta\Code\Internal::class)
	)
)
->Each(function(Nether\Meta\Sensei\ClassMethodDoc $Method) {
	printf(
		"## %s\n",
		$Method->Name
	);

	printf(
		"```\n%s( %s ): %s;\n```\n",
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

	echo "\n";
	echo "{$Method->GetInfo()}\n\n";
	echo "\n";
	return;
});

////////////////////////////////////////////////////////////////////////////////

$Buffer = ob_get_clean();
file_put_contents($Outfile, $Buffer);

echo $Buffer;

////////////////////////////////////////////////////////////////////////////////
