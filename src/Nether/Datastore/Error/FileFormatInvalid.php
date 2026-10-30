<?php ##########################################################################
################################################################################

namespace Nether\Datastore\Error;

use Exception;

################################################################################
################################################################################

class FileFormatInvalid
extends Exception {

	public function
	__Construct(string $Filename) {
		parent::__Construct(sprintf('file format invalid: %s', $Filename));
		return;
	}

};
