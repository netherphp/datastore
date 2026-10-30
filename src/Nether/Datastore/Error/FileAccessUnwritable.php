<?php ##########################################################################
################################################################################

namespace Nether\Datastore\Error;

use Exception;

################################################################################
################################################################################

class FileAccessUnwritable
extends Exception {

	public function
	__Construct(string $Filename) {
		parent::__Construct(sprintf('file access unwritable: %s', $Filename));
		return;
	}

};
