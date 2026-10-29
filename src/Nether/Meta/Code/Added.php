<?php ##########################################################################
################################################################################

namespace Nether\Meta\Code;

use Nether;
use Attribute;

################################################################################
################################################################################

#[Attribute]
class Added {

	public ?string
	$Date;

	public ?string
	$Version;

	public function
	__Construct(?string $Date=NULL, ?string $Version=NULL) {

		$this->Date = $Date;
		$this->Version = $Version;

		return;
	}

};
