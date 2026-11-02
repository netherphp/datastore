<?php ##########################################################################
################################################################################

namespace Nether\Meta\Code;

use Nether;
use Attribute;

################################################################################
################################################################################

#[Attribute]
class Todo {

	public ?string
	$Date;

	public ?string
	$Message;

	public function
	__Construct(?string $Date=NULL, ?string $Message=NULL) {

		$this->Date = $Date;
		$this->Message = $Message;

		return;
	}

};
