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

};
