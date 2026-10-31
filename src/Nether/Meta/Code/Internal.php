<?php ##########################################################################
################################################################################

namespace Nether\Meta\Code;

use Attribute;

################################################################################
################################################################################

// vscode intelephense ignores the phpdoc @internal where it should be using
// that flag to make it skip those methods from auto complete. so there is
// really no point and i might as well commit to my annotations.

#[Attribute]
class Internal {

};
