<?php ##########################################################################
################################################################################

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Nether\Datastore;

################################################################################
################################################################################

class DatastoreTest
extends TestCase {

	const array
	DatasetSimpleList = [ 'one', 'two', 'three' ];

	const array
	DatasetSimpleMap = [ 'one' => 1, 'two' => 2, 'three' => 3 ];

	const int
	DatasetSimpleCount = 3;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Test]
	public function
	FromEmptyNormal():
	void {

		$Data = new Datastore;
		$this->assertInstanceOf(Datastore::class, $Data);
		$this->assertIsArray($Data->GetData());
		$this->assertCount(0, $Data->GetData());

		return;
	}

	#[Test]
	public function
	FromEmptyFactory():
	void {

		$Data = Datastore::New();
		$this->assertCount(0, $Data->GetData());

		return;
	}

	#[Test]
	public function
	FromArrayNormal():
	void {

		$Data = new Datastore(static::DatasetSimpleList);
		$this->assertCount(static::DatasetSimpleCount, $Data->GetData());

		return;
	}

	#[Test]
	public function
	FromArrayFactory():
	void {

		$Data = Datastore::New(static::DatasetSimpleList);
		$this->assertCount(static::DatasetSimpleCount, $Data->GetData());

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Test]
	public function
	CoolWithLists():
	void {

		$Store = new Datastore(static::DatasetSimpleList);
		$Data = $Store->GetData();

		$this->assertArrayHasKey(0, $Data);
		$this->assertCount(static::DatasetSimpleCount, $Data);
		$this->assertEquals('one', $Data[0]);
		
		return;
	}

	#[Test]
	public function
	CoolWithMaps():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Data = $Store->GetData();

		$this->assertArrayHasKey('one', $Data);
		$this->assertCount(static::DatasetSimpleCount, $Data);
		$this->assertEquals(1, $Data['one']);
		
		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Test]
	public function
	MethodHas():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$this->assertTrue($Store->Has('one'));
		$this->assertFalse($Store->Has('zero'));

		return;
	}

	#[Test]
	public function
	MethodGet():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$this->assertEquals(1, $Store->Get('one'));

		$this->assertNull($Store->Get('zero'));
		$this->assertFalse($Store->Get('zero', FALSE));
	
		return;
	}

	#[Test]
	public function
	MethodSet():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$this->assertEquals(1, $Store->Get('one'));

		// expect a changed value.

		$Store->Set('one', '一');
		$this->assertEquals('一', $Store->Get('one'));
	
		return;
	}

	#[Test]
	public function
	MethodDefine():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$this->assertEquals(1, $Store->Get('one'));

		// expect an unchanged value.

		$this->assertTrue($Store->Has('one'));
		$Store->Define('one', '一');
		$this->assertEquals(1, $Store->Get('one'));

		// expect a freshly set value.

		$this->assertFalse($Store->Has('zero'));
		$Store->Define('zero', '〇');

		$this->assertTrue($Store->Has('zero'));
		$this->assertEquals('〇', $Store->Get('zero'));
	
		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Test]
	public function
	MethodEach():
	void {

		$Func = function(int $V) {
			echo $V, PHP_EOL;
			return;
		};

		$Expect = join(PHP_EOL, [ 1, 2, 3 ]) . PHP_EOL;

		$Test = function(string $Buffer) use($Expect) {
			$this->assertEquals($Expect, $Buffer);
			return;
		};

		////////

		ob_start($Test);
			$Store = new Datastore(static::DatasetSimpleMap);
			$Store->Each($Func);
		ob_end_flush();

		return;
	}

	#[Test]
	public function
	MethodEachKeyValue():
	void {

		$Func = function(string $K, int $V) {
			echo "{$K}:{$V}", PHP_EOL;
			return;
		};

		$Expect = join(PHP_EOL, [ 'one:1', 'two:2', 'three:3' ]) . PHP_EOL;

		$Test = function(string $Buffer) use($Expect) {
			$this->assertEquals($Expect, $Buffer);
		};

		////////

		ob_start($Test);
			$Store = new Datastore(static::DatasetSimpleMap);
			$Store->EachKeyValue($Func);
		ob_end_flush();

		return;
	}

};
