<?php ##########################################################################
################################################################################

namespace Nether\Filter;

use Nether\Meta\Code;

################################################################################
################################################################################

class Type {

	#[Code\Info('Check if a value seems nullable. (NULL, "NULL", and "")')]
	#[Code\Added('2026-11-03')]
	static public function
	IsNullable(mixed $Item):
	bool {

		if(is_null($Item))
		return TRUE;

		////////

		$Normal = match(TRUE) {
			is_string($Item)
			=> strtoupper(substr(trim($Item), 0, 4)),

			default
			=> $Item
		};

		$Output = match($Normal) {
			'NULL', '',
			=> TRUE,

			default
			=> FALSE
		};

		////////

		return $Output;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Code\Info('Return a boolean for input that seems boolean.')]
	#[Code\Added('2026-11-03')]
	static public function
	Bool(mixed $Item):
	bool {

		if(is_bool($Item))
		return $Item;

		////////

		$Normal = match(TRUE) {
			is_string($Item)
			=> strtoupper(substr(trim($Item), 0, 5)),

			default
			=> $Item
		};

		$Output = match($Normal) {

			// specific truies.
			1, '1', 'T', 'TRUE', 'Y', 'YES'
			=> TRUE,

			// specific falsies.
			NULL, 0, '0', 'F', 'FALSE', 'N', 'NO',
			=> FALSE,

			// anything undefined is also false.
			default
			=> FALSE

		};

		////////

		return $Output;
	}

	#[Code\Info('Return a boolean for input that seems boolean, with nullable conditions.')]
	#[Code\Added('2026-11-03')]
	static public function
	BoolNullable(mixed $Item):
	?bool {

		if(static::IsNullable($Item))
		return NULL;

		return static::Bool($Item);
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

		if(static::IsNullable($Item))
		return NULL;

		return static::Int($Item);
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

		if(static::IsNullable($Item))
		return NULL;

		return static::Float($Item);
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

		if(static::IsNullable($Item))
		return NULL;

		return static::String($Item);
	}

};
