<?php ##########################################################################
################################################################################

namespace Nether\Filter;

use Nether\Meta\Code;

################################################################################
################################################################################

class Numbers {

	#[Code\Info('Always return an Int within range 1 to PHP_INT_MAX.')]
	static public function
	Page(mixed $Item):
	int {

		$Item = Type::Int($Item);

		if($Item < 1)
		return 1;

		return $Item;
	}

	#[Code\Info('String to Int. Supports decimal, octal 0o, hex 0x, and binary 0b.')]
	#[Code\Added('2027-11-03')]
	static public function
	Numeric(mixed $Val):
	int {

		if(is_int($Val))
		return $Val;

		////////

		$Result = match(TRUE) {
			(is_string($Val))
			=>  match(TRUE) {
				str_starts_with($Val, '0o'),
				=> octdec($Val),

				str_starts_with($Val, '0x')
				=> hexdec($Val),

				str_starts_with($Val, '0b')
				=> bindec($Val),

				default
				=> Type::Int($Val)
			},

			default
			=> Type::Int($Val)
		};

		////////

		return $Result;
	}

};
