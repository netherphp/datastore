<?php ##########################################################################
################################################################################

namespace Nether\Filter;

use Nether\Meta\Code;

################################################################################
################################################################################

class Type {

	#[Code\Info('Return a boolean for input that seems boolean.')]
	#[Code\Added('2026-11-03')]
	static public function
	Bool(mixed $Item):
	bool {

		if(is_bool($Item))
		return $Item;

		if(is_string($Item) && (strlen($Item) <= 4))
		$Item = strtoupper(trim($Item));

		return match($Item) {
			1, '1', 'T', 'TRUE', 'Y', 'YES'
			=> TRUE,

			default
			=> FALSE
		};
	}

	#[Code\Info('Return a boolean for input that seems boolean, with nullable conditions.')]
	#[Code\Added('2026-11-03')]
	static public function
	BoolNullable(mixed $Item):
	?bool {

		if($Item === NULL)
		return NULL;

		if(is_bool($Item))
		return $Item;

		if(is_string($Item))
		$Item = strtoupper(trim($Item));

		return match($Item) {
			1, '1', 'T', 'TRUE', 'Y', 'YES'
			=> TRUE,

			'NULL', '',
			=> NULL,

			default
			=> FALSE
		};
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Code\Info('Return a int for input that seems like a number.')]
	#[Code\Added('2026-11-03')]
	static public function
	Int(mixed $Item):
	int {

		if(is_int($Item))
		return $Item;

		if(is_scalar($Item))
		return (int)$Item;

		return 0;
	}

	#[Code\Info('Return a int for input that seems like a number, with nullable conditions.')]
	#[Code\Added('2026-11-03')]
	static public function
	IntNullable(mixed $Item):
	?int {

		if($Item === NULL)
		return NULL;

		if(is_string($Item))
		$Item = strtoupper(trim($Item));

		return match($Item) {
			'NULL', '',
			=> NULL,

			default
			=> static::Int($Item)
		};
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Code\Info('Return a float for input that seems like a number.')]
	#[Code\Added('2026-11-03')]
	static public function
	Float(mixed $Item):
	float {

		if(is_float($Item))
		return $Item;

		if(is_scalar($Item))
		return (float)$Item;

		return 0.0;
	}

	#[Code\Info('Return a float for input that seems like a number, with nullable conditions.')]
	#[Code\Added('2026-11-03')]
	static public function
	FloatNullable(mixed $Item):
	?float {

		if($Item === NULL)
		return NULL;

		if(is_string($Item))
		$Item = strtoupper(trim($Item));

		return match($Item) {
			'NULL', '',
			=> NULL,

			default
			=> static::Float($Item)
		};
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Code\Info('Return a string for stringable things.')]
	#[Code\Added('2026-11-03')]
	static public function
	String(mixed $Item):
	string {

		if(is_string($Item))
		return $Item;

		////////

		if(is_bool($Item))
		return ($Item ? 'TRUE' : 'FALSE');

		if(is_null($Item))
		return 'NULL';

		////////

		return (string)$Item;
	}

	#[Code\Info('Return a string for stringable things, with nullable conditions.')]
	#[Code\Added('2026-11-03')]
	static public function
	StringNullable(mixed $Item):
	?string {

		if($Item === NULL)
		return NULL;

		////////

		$Norm = match(TRUE) {
			is_string($Item)
			=> strtoupper(substr(trim($Item), 0, 4)),

			default
			=> $Item
		};

		return match($Norm) {
			'NULL', '',
			=> NULL,

			default
			=> static::String($Item)
		};
	}

};
