<?php ##########################################################################
################################################################################

namespace Nether\Datastore\Error;

use Exception;

use function PHPSTORM_META\type;

################################################################################
################################################################################

class ReindexFormatInvalid
extends Exception {

	public function
	__Construct(mixed $Value) {
		
		parent::__Construct(sprintf(
			'callable return must be <int|string|array<int|string, mixed>|NULL|FALSE> (%s given)',
			gettype($Value)
		));

		return;
	}

};
