<?php ##########################################################################
################################################################################

namespace Nether\Meta\Code;

use Attribute;

################################################################################
################################################################################

#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_ALL)]
class Arg {

	public string
	$Name;

	public string
	$Type;

	public string
	$Info;

	public function
	__Construct(string $Name, ?string $Type=NULL, ?string $Info=NULL) {

		$this->Name = $Name;
		$this->Type = $Type ?? 'mixed';
		$this->Info = $Info ?? '';

		return;
	}

};
