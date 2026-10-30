<?php ##########################################################################
################################################################################

namespace Nether\Datastore\Error;

use Exception;

################################################################################
################################################################################

class FileNotFound
extends Exception {

	public function
	__Construct(string $Filename) {
		parent::__Construct(sprintf('file not found: %s', $Filename));
		return;
	}

};
