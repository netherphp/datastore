<?php ##########################################################################
################################################################################

namespace Nether\Datastore\Error;

use Exception;

################################################################################
################################################################################

class FileAccessUnreadable
extends Exception {

	public function
	__Construct(string $Filename) {
		parent::__Construct(sprintf('file access unreadable: %s', $Filename));
		return;
	}

};
