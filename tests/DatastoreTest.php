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
		$this->assertIsArray($Data->Export());
		$this->assertCount(0, $Data->Export());

		return;
	}

	#[Test]
	public function
	FromEmptyFactory():
	void {

		$Data = Datastore::New();
		$this->assertCount(0, $Data->Export());

		return;
	}

	#[Test]
	public function
	FromArrayNormal():
	void {

		$Data = new Datastore(static::DatasetSimpleList);
		$this->assertCount(static::DatasetSimpleCount, $Data->Export());

		return;
	}

	#[Test]
	public function
	FromArrayFactory():
	void {

		$Data = Datastore::New(static::DatasetSimpleList);
		$this->assertCount(static::DatasetSimpleCount, $Data->Export());

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Test]
	public function
	CoolWithLists():
	void {

		$Store = new Datastore(static::DatasetSimpleList);
		$Data = $Store->Export();

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
		$Data = $Store->Export();

		$this->assertArrayHasKey('one', $Data);
		$this->assertCount(static::DatasetSimpleCount, $Data);
		$this->assertEquals(1, $Data['one']);
		
		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Test]
	public function
	MethodWriteJSON():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Other = new Datastore;
		$Filename = sprintf('%s.json', @tempnam('temp', 'dt-'));
		$Expect = json_encode($Store->Export());

		// test writing a file to disk.

		$Store->Write($Filename);
		$this->assertTrue(file_exists($Filename));
		$this->assertGreaterThan(1, filesize($Filename));
		$this->assertEquals($Expect, file_get_contents($Filename));

		// test loading that file from disk.

		$Other->Load($Filename);
		$this->assertCount(static::DatasetSimpleCount, $Other);
		$this->assertEquals(3, $Other->Get('three'));

		////////

		unlink($Filename);

		return;
	}

	#[Test]
	public function
	MethodWritePHSON():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Other = new Datastore;
		$Filename = sprintf('%s.phson', @tempnam('temp', 'dt-'));
		$Expect = serialize($Store->Export());

		// test writing a file to disk.

		$Store->Write($Filename);
		$this->assertTrue(file_exists($Filename));
		$this->assertGreaterThan(1, filesize($Filename));
		$this->assertEquals($Expect, file_get_contents($Filename));

		// test loading that file from disk.

		$Other->Load($Filename);
		$this->assertCount(static::DatasetSimpleCount, $Other);
		$this->assertEquals(3, $Other->Get('three'));

		////////

		unlink($Filename);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Test]
	public function
	MethodCount():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$this->assertEquals(static::DatasetSimpleCount, $Store->Count());
		
		return;
	}

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
	MethodUnset():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$this->assertTrue($Store->Has('two'));

		$Store->Unset('two');
		$this->assertFalse($Store->Has('two'));

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

	#[Test]
	public function
	MethodPush():
	void {

		$Store = new Datastore;
		$Store->Push(1)->Push(2)->Push(3);

		$this->assertEquals(3, $Store->Count());
		$this->assertEquals(1, $Store->Get(0));
		$this->assertEquals(3, $Store->Get(2));

		return;
	}

	#[Test]
	public function
	MethodPop():
	void {

		$Store = new Datastore(static::DatasetSimpleList);
		$this->assertEquals(3, $Store->Count());

		$Value = $Store->Pop();
		$this->assertEquals('three', $Value);
		$this->assertEquals(2, $Store->Count());

		return;
	}

	#[Test]
	public function
	MethodPushFirst():
	void {

		$Store = new Datastore;
		$Store->PushFirst(1)->PushFirst(2)->PushFirst(3);

		$this->assertEquals(3, $Store->Count());
		$this->assertEquals(3, $Store->Get(0));
		$this->assertEquals(1, $Store->Get(2));

		return;
	}

	#[Test]
	public function
	MethodPopFirst():
	void {

		$Store = new Datastore(static::DatasetSimpleList);
		$this->assertEquals(3, $Store->Count());

		$Value = $Store->PopFirst();
		$this->assertEquals('one', $Value);
		$this->assertEquals(2, $Store->Count());

		return;
	}

	#[Test]
	public function
	MethodIsList():
	void {

		$List = new Datastore(static::DatasetSimpleList);
		$Map = new Datastore(static::DatasetSimpleMap);

		$this->assertTrue($List->IsList());
		$this->assertFalse($Map->IsList());

		return;
	}

	#[Test]
	public function
	MethodListify():
	void {

		$Map = new Datastore(static::DatasetSimpleMap);
		$this->assertFalse($Map->IsList());

		$Map->Listify();
		$this->assertTrue($Map->IsList());

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
			return;
		};

		////////

		ob_start($Test);
			$Store = new Datastore(static::DatasetSimpleMap);
			$Store->EachKeyValue($Func);
		ob_end_flush();

		return;
	}

	#[Test]
	public function
	MethodFilter():
	void {

		$Store = new Datastore(static::DatasetSimpleList);
		$Store->Filter(fn($V)=> $V === 'two')->Listify();

		$this->assertEquals(1, $Store->Count());
		$this->assertEquals('two', $Store->Get(0));

		return;
	}

	#[Test]
	public function
	MethodFilterKeyValue():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Store->FilterKeyValue(fn($K, $V)=> $K === 'two');

		$this->assertEquals(1, $Store->Count());
		$this->assertEquals(2, $Store->Get('two'));
		$this->assertNull($Store->Get('one'));

		return;
	}

	#[Test]
	public function
	MethodDistill():
	void {

		$Store = new Datastore(static::DatasetSimpleList);
		$Other = $Store->Distill(fn($V)=> $V === 'two')->Listify();

		$this->assertNotEquals(
			spl_object_id($Store),
			spl_object_id($Other)
		);

		$this->assertEquals(3, $Store->Count());
		$this->assertEquals('one', $Store->Get(0));

		$this->assertEquals(1, $Other->Count());
		$this->assertEquals('two', $Other->Get(0));

		return;
	}

	#[Test]
	public function
	MethodDistillKeyValue():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Other = $Store->DistillKeyValue(fn($K, $V)=> $K === 'two');

		$this->assertNotEquals(
			spl_object_id($Store),
			spl_object_id($Other)
		);

		$this->assertEquals(3, $Store->Count());
		$this->assertEquals(1, $Store->Get('one'));

		$this->assertEquals(1, $Other->Count());
		$this->assertEquals(2, $Other->Get('two'));
		$this->assertNull($Other->Get('one'));

		return;
	}

	#[Test]
	public function
	MethodMap():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Other = $Store->Map(fn(int $Val)=> $Val * $Val);

		$this->assertNotEquals(
			spl_object_id($Other),
			spl_object_id($Store)
		);

		$this->assertEquals(1, $Store->Get('one'));
		$this->assertEquals(2, $Store->Get('two'));
		$this->assertEquals(3, $Store->Get('three'));
		
		$this->assertEquals(1, $Other->Get('one'));
		$this->assertEquals(4, $Other->Get('two'));
		$this->assertEquals(9, $Other->Get('three'));
		
		return;
	}

	#[Test]
	public function
	MethodMapKeyValue():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		
		$Other = $Store->MapKeyValue(
			fn(int|string $Key, int $Val)
			=> sprintf('%s:%d', $Key, ($Val * $Val))
		);

		$this->assertNotEquals(
			spl_object_id($Other),
			spl_object_id($Store)
		);

		$this->assertEquals(1, $Store->Get('one'));
		$this->assertEquals(2, $Store->Get('two'));
		$this->assertEquals(3, $Store->Get('three'));

		$this->assertEquals('one:1', $Other->Get('one'));
		$this->assertEquals('two:4', $Other->Get('two'));
		$this->assertEquals('three:9', $Other->Get('three'));
		
		return;
	}

	#[Test]
	public function
	MethodRemap():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Store->Remap(fn(int $Val)=> $Val * $Val);
		
		$this->assertEquals(1, $Store->Get('one'));
		$this->assertEquals(4, $Store->Get('two'));
		$this->assertEquals(9, $Store->Get('three'));
		
		return;
	}

	#[Test]
	public function
	MethodRemapKeyValue():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		
		$Store->RemapKeyValue(
			fn(int|string $Key, int $Val)
			=> sprintf('%s:%d', $Key, ($Val * $Val))
		);

		$this->assertEquals('one:1', $Store->Get('one'));
		$this->assertEquals('two:4', $Store->Get('two'));
		$this->assertEquals('three:9', $Store->Get('three'));
		
		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Test]
	public function
	InterfaceArrayAccess():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);

		// test offsetExists

		$this->assertTrue(isset($Store['two']));
		$this->assertFalse(isset($Store['four']));

		// test offsetGet

		$this->assertEquals(2, $Store['two']);

		// test offsetSet

		$Store['four'] = 4;
		$this->assertTrue(isset($Store['four']));
		$this->assertEquals(4, $Store['four']);
		
		// test offsetUnset

		unset($Store['two']);
		$this->assertFalse(isset($Store['two']));
		

		return;
	}

	#[Test]
	public function
	InterfaceIterator():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		
		$Expect = join(PHP_EOL, [ 'one:1', 'two:2', 'three:3' ]) . PHP_EOL;

		$Test = function(string $Buffer) use($Expect) {
			$this->assertEquals($Expect, $Buffer);
			return;
		};

		ob_start($Test);
		foreach($Store as $Key => $Val) {
			printf('%s:%d%s', $Key, $Val, PHP_EOL);
		}
		ob_end_flush();

		return;
	}

	#[Test]
	public function
	InterfaceCountable():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
	
		$this->assertEquals(static::DatasetSimpleCount, count($Store));

		return;
	}


};
