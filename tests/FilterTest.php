<?php ##########################################################################
################################################################################

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Nether\Filter;

////////////////////////////////////////////////////////////////////////////////

class FilterTest
extends TestCase {

	#[Test]
	public function
	TypeBool():
	void {

		$this->assertTrue(Filter\Type::Bool(TRUE));
		$this->assertTrue(Filter\Type::Bool(1));
		$this->assertTrue(Filter\Type::Bool('1'));
		$this->assertTrue(Filter\Type::Bool('Y'));
		$this->assertTrue(Filter\Type::Bool('YES'));
		$this->assertTrue(Filter\Type::Bool('T'));
		$this->assertTrue(Filter\Type::Bool('TRUE'));

		$this->assertFalse(Filter\Type::Bool(FALSE));
		$this->assertFalse(Filter\Type::Bool(0));
		$this->assertFalse(Filter\Type::Bool(0.0));
		$this->assertFalse(Filter\Type::Bool(1.0));
		$this->assertFalse(Filter\Type::Bool('0'));
		$this->assertFalse(Filter\Type::Bool('N'));
		$this->assertFalse(Filter\Type::Bool('NO'));
		$this->assertFalse(Filter\Type::Bool('F'));
		$this->assertFalse(Filter\Type::Bool('FALSE'));

		$this->assertFalse(Filter\Type::Bool(NULL));
		$this->assertFalse(Filter\Type::Bool('null'));
		$this->assertFalse(Filter\Type::Bool('Null'));
		$this->assertFalse(Filter\Type::Bool('NULL'));
		$this->assertFalse(Filter\Type::Bool(''));
		$this->assertFalse(Filter\Type::Bool('asdf'));
		$this->assertFalse(Filter\Type::Bool([]));
		$this->assertFalse(Filter\Type::Bool(new stdClass));

		return;
	}

	#[Test]
	public function
	TypeBoolNullable():
	void {

		$this->assertNull(Filter\Type::BoolNullable(NULL));
		$this->assertNull(Filter\Type::BoolNullable('NULL'));
		$this->assertNull(Filter\Type::BoolNullable('null'));
		$this->assertNull(Filter\Type::BoolNullable(''));
		$this->assertNull(Filter\Type::BoolNullable('    '));

		$this->assertTrue(Filter\Type::BoolNullable(TRUE));
		$this->assertTrue(Filter\Type::BoolNullable(1));
		$this->assertTrue(Filter\Type::BoolNullable('1'));
		$this->assertTrue(Filter\Type::BoolNullable('Y'));
		$this->assertTrue(Filter\Type::BoolNullable('T'));
		$this->assertTrue(Filter\Type::BoolNullable('TRUE'));

		$this->assertFalse(Filter\Type::BoolNullable(FALSE));
		$this->assertFalse(Filter\Type::BoolNullable(0));
		$this->assertFalse(Filter\Type::BoolNullable('0'));
		$this->assertFalse(Filter\Type::BoolNullable('N'));
		$this->assertFalse(Filter\Type::BoolNullable('F'));
		$this->assertFalse(Filter\Type::BoolNullable('FALSE'));

		$this->assertFalse(Filter\Type::BoolNullable('asdf'));
		$this->assertFalse(Filter\Type::BoolNullable([]));
		$this->assertFalse(Filter\Type::BoolNullable(new stdClass));

		return;
	}

	////////////////////////////////////////////////////////////////

	#[Test]
	public function
	TypeInt():
	void {

		$this->assertSame(0, Filter\Type::Int(NULL));

		$this->assertSame(0, Filter\Type::Int(0));
		$this->assertSame(0, Filter\Type::Int(0.0));
		$this->assertSame(0, Filter\Type::Int(0.2));
		$this->assertSame(0, Filter\Type::Int(0.8));

		$this->assertSame(1, Filter\Type::Int(1));
		$this->assertSame(1, Filter\Type::Int(1.0));
		$this->assertSame(1, Filter\Type::Int(1.2));
		$this->assertSame(1, Filter\Type::Int(1.8));

		$this->assertSame(69, Filter\Type::Int('69.0'));
		
		$this->assertSame(0, Filter\Type::Int('asdf'));
		$this->assertSame(0, Filter\Type::Int([]));
		$this->assertSame(0, Filter\Type::Int(new stdClass));
		$this->assertSame(0, Filter\Type::Int('0x1'));
		$this->assertSame(0, Filter\Type::Int('0b1'));
		$this->assertSame(0, Filter\Type::Int('0o1'));

		return;
	}

	#[Test]
	public function
	TypeIntNullable():
	void {

		$this->assertNull(Filter\Type::IntNullable(NULL));
		$this->assertNull(Filter\Type::IntNullable('NULL'));
		$this->assertNull(Filter\Type::IntNullable('null'));
		$this->assertNull(Filter\Type::IntNullable(' '));
		$this->assertNull(Filter\Type::IntNullable('    '));

		$this->assertSame(0, Filter\Type::IntNullable(0));
		$this->assertSame(0, Filter\Type::IntNullable(0.0));
		$this->assertSame(0, Filter\Type::IntNullable(0.2));
		$this->assertSame(0, Filter\Type::IntNullable(0.8));

		$this->assertSame(1, Filter\Type::IntNullable(1));
		$this->assertSame(1, Filter\Type::IntNullable(1.0));
		$this->assertSame(1, Filter\Type::IntNullable(1.2));
		$this->assertSame(1, Filter\Type::IntNullable(1.8));

		$this->assertSame(69, Filter\Type::IntNullable('69.0'));
		
		$this->assertSame(0, Filter\Type::IntNullable('asdf'));
		$this->assertSame(0, Filter\Type::IntNullable([]));
		$this->assertSame(0, Filter\Type::IntNullable(new stdClass));
		$this->assertSame(0, Filter\Type::IntNullable('0x1'));
		$this->assertSame(0, Filter\Type::IntNullable('0b1'));
		$this->assertSame(0, Filter\Type::IntNullable('0o1'));

		return;
	}

	////////////////////////////////////////////////////////////////

	#[Test]
	public function
	TypeFloat():
	void {

		$this->assertSame(0.0, Filter\Type::Float(NULL));
		$this->assertSame(0.0, Filter\Type::Float('NULL'));
		$this->assertSame(0.0, Filter\Type::Float('null'));

		$this->assertSame(0.0, Filter\Type::Float(0));
		$this->assertSame(0.0, Filter\Type::Float(0.0));
		$this->assertSame(0.2, Filter\Type::Float(0.2));
		$this->assertSame(0.8, Filter\Type::Float(0.8));
		
		$this->assertSame(1.0, Filter\Type::Float(1));
		$this->assertSame(1.0, Filter\Type::Float(1.0));
		$this->assertSame(1.2, Filter\Type::Float(1.2));
		$this->assertSame(1.8, Filter\Type::Float(1.8));

		$this->assertSame(6.9, Filter\Type::Float('6.9'));

		$this->assertSame(0.0, Filter\Type::Float('asdf'));
		$this->assertSame(0.0, Filter\Type::Float([]));
		$this->assertSame(0.0, Filter\Type::Float(new stdClass));

		return;
	}

	#[Test]
	public function
	TypeFloatNullable():
	void {

		$this->assertNull(Filter\Type::FloatNullable(NULL));
		$this->assertNull(Filter\Type::FloatNullable('NULL'));
		$this->assertNull(Filter\Type::FloatNullable('null'));
		$this->assertNull(Filter\Type::FloatNullable(' '));
		$this->assertNull(Filter\Type::FloatNullable('    '));

		$this->assertSame(0.0, Filter\Type::FloatNullable(0));
		$this->assertSame(0.0, Filter\Type::FloatNullable(0.0));
		$this->assertSame(0.2, Filter\Type::FloatNullable(0.2));
		$this->assertSame(0.8, Filter\Type::FloatNullable(0.8));
		
		$this->assertSame(1.0, Filter\Type::FloatNullable(1));
		$this->assertSame(1.0, Filter\Type::FloatNullable(1.0));
		$this->assertSame(1.2, Filter\Type::FloatNullable(1.2));
		$this->assertSame(1.8, Filter\Type::FloatNullable(1.8));

		$this->assertSame(6.9, Filter\Type::FloatNullable('6.9'));

		$this->assertSame(0.0, Filter\Type::FloatNullable('asdf'));
		$this->assertSame(0.0, Filter\Type::FloatNullable([]));
		$this->assertSame(0.0, Filter\Type::FloatNullable(new stdClass));

		return;
	}

	////////////////////////////////////////////////////////////////

	#[Test]
	public function
	TypeString():
	void {

		
		$this->assertSame('', Filter\Type::String(''));
		$this->assertSame('test', Filter\Type::String('test'));
		$this->assertSame('1', Filter\Type::String(1));
		$this->assertSame('1', Filter\Type::String(1.0));
		$this->assertSame('1.2', Filter\Type::String(1.2));

		$this->assertSame('TRUE', Filter\Type::String('TRUE'));
		$this->assertSame('True', Filter\Type::String('True'));
		$this->assertSame('FALSE', Filter\Type::String('FALSE'));
		$this->assertSame('False', Filter\Type::String('False'));
		$this->assertSame('NULL', Filter\Type::String('NULL'));
		$this->assertSame('null', Filter\Type::String('null'));

		// literal types printed

		$this->assertSame('NULL', Filter\Type::String(NULL));
		$this->assertSame('TRUE', Filter\Type::String(TRUE));
		$this->assertSame('FALSE', Filter\Type::String(FALSE));

		return;
	}

	#[Test]
	public function
	TypeStringNullable():
	void {

		$this->assertNull(Filter\Type::StringNullable(''));
		$this->assertNull(Filter\Type::StringNullable(NULL));
		$this->assertNull(Filter\Type::StringNullable('NULL'));
		$this->assertNull(Filter\Type::StringNullable('null'));

		$this->assertSame('test', Filter\Type::StringNullable('test'));
		$this->assertSame('1', Filter\Type::StringNullable(1));
		$this->assertSame('1', Filter\Type::StringNullable(1.0));
		$this->assertSame('1.2', Filter\Type::StringNullable(1.2));

		$this->assertSame('TRUE', Filter\Type::StringNullable('TRUE'));
		$this->assertSame('True', Filter\Type::StringNullable('True'));
		$this->assertSame('FALSE', Filter\Type::StringNullable('FALSE'));
		$this->assertSame('False', Filter\Type::StringNullable('False'));

		// literal types printed

		$this->assertSame('TRUE', Filter\Type::StringNullable(TRUE));
		$this->assertSame('FALSE', Filter\Type::StringNullable(FALSE));

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Test]
	public function
	NumbersNumeric():
	void {

		$this->assertSame(0, Filter\Numbers::Numeric(NULL));

		$this->assertSame(0, Filter\Numbers::Numeric(0));
		$this->assertSame(0, Filter\Numbers::Numeric(0.0));
		$this->assertSame(0, Filter\Numbers::Numeric(0.2));
		$this->assertSame(0, Filter\Numbers::Numeric(0.8));

		$this->assertSame(1, Filter\Numbers::Numeric(1));
		$this->assertSame(1, Filter\Numbers::Numeric(1.0));
		$this->assertSame(1, Filter\Numbers::Numeric(1.2));
		$this->assertSame(1, Filter\Numbers::Numeric(1.8));

		$this->assertSame(69, Filter\Numbers::Numeric('69.0'));
		
		$this->assertSame(0, Filter\Numbers::Numeric('asdf'));
		$this->assertSame(0, Filter\Numbers::Numeric([]));
		$this->assertSame(0, Filter\Numbers::Numeric(new stdClass));

		$this->assertSame(1, Filter\Numbers::Numeric('0x1'));
		$this->assertSame(1, Filter\Numbers::Numeric('0b1'));
		$this->assertSame(1, Filter\Numbers::Numeric('0o1'));

		return;
	}

	#[Test]
	public function
	NumbersPage():
	void {

		$this->assertSame(1, Filter\Numbers::Page(0));
		$this->assertSame(1, Filter\Numbers::Page(1));
		$this->assertSame(1, Filter\Numbers::Page(1.2));
		$this->assertSame(1, Filter\Numbers::Page(1.8));
		$this->assertSame(1, Filter\Numbers::Page('1'));
		$this->assertSame(1, Filter\Numbers::Page('1.8'));

		$this->assertSame(2, Filter\Numbers::Page(2));
		$this->assertSame(2, Filter\Numbers::Page(2.2));
		$this->assertSame(2, Filter\Numbers::Page(2.8));
		$this->assertSame(2, Filter\Numbers::Page('2'));
		$this->assertSame(2, Filter\Numbers::Page('2.8'));


		return;
	}


};
