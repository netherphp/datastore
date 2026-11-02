<?php ##########################################################################
################################################################################

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Nether\Datastore;

////////////////////////////////////////////////////////////////////////////////

// this is a work around where at least on windows, files created by tempnam()
// cannot be unlink()'d even though the same user running the test can manually
// delete them from that same terminal afterwards.

function
LocalTempDir():
string {

	$TempDir = sys_get_temp_dir();

	if(!is_dir($TempDir))
	mkdir($TempDir, 0777);

	////////

	return $TempDir;
};

function
LocalTouchTempFile(string $Prefix, string $Ext='tmp'):
string {

	$Filename = sprintf(
		'%s%s%s-%s.%s',
		LocalTempDir(),
		DIRECTORY_SEPARATOR,
		$Prefix,
		md5(microtime(TRUE)),
		$Ext
	);

	touch($Filename);

	////////

	return $Filename;
};

function
LocalLockFile(string $Filename):
void {

	if(PHP_OS_FAMILY === 'Windows')
	exec(sprintf(
		'icacls %s /deny Everyone:RW',
		escapeshellarg($Filename)
	));

	else
	chmod($Filename, 0000);

	return;
};

function
LocalUnlockFile(string $Filename):
void {

	if(PHP_OS_FAMILY === 'Windows')
	exec(sprintf(
		'icacls %s /remove:d Everyone',
		escapeshellarg($Filename)
	));

	else
	chmod($Filename, 0666);

	return;
};

################################################################################
################################################################################

class DatastoreTest
extends TestCase {

	const array
	DatasetSimpleList = [ 'one', 'two', 'three' ];

	const array
	DatasetSimpleMap = [ 'one' => 1, 'two' => 2, 'three' => 3 ];

	const array
	DatasetSimpleMapOverlap = [ 'three' => -3, 'four' => 4, 'five' => 5 ];

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

	#[Test]
	public function
	FromIterableNormal():
	void {

		$Iter = new ArrayIterator(static::DatasetSimpleList);
		$Data = new Datastore($Iter);

		$this->assertCount(static::DatasetSimpleCount, $Data->Export());

		return;
	}

	#[Test]
	public function
	FromIterableFactory():
	void {

		$Iter = new ArrayIterator(static::DatasetSimpleList);
		$Data = Datastore::New($Iter);

		$this->assertCount(static::DatasetSimpleCount, $Data->Export());

		return;
	}

	#[Test]
	public function
	FromStringFactory():
	void {

		$Store = Datastore::FromString('asdf');
		$this->assertEquals(4, $Store->Count());

		$Store = Datastore::FromString('as,df', ',');
		$this->assertEquals(2, $Store->Count());

		$Store = Datastore::FromString('as,df', '');
		$this->assertEquals(5, $Store->Count());

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
	MethodLoad_FileNotFound():
	void {

		$Store = new Datastore;
		$Filename = 'asdfasdfasdfasdfasdf.lol';
		$Err = NULL;

		try { $Store->Load($Filename); }
		catch(Exception $Err) { }

		$this->assertInstanceOf(
			Datastore\Error\FileNotFound::class,
			$Err
		);

		return;
	}

	#[Test]
	public function
	MethodLoad_FileUnreadable():
	void {

		$Store = new Datastore;
		$Filename = LocalTouchTempFile('nether-datastore-readlocked');
		$Err = NULL;

		////////

		LocalLockFile($Filename);

		try { $Store->Load($Filename); }
		catch(Exception $Err) { }

		LocalUnlockFile($Filename);
		unlink($Filename);

		$this->assertInstanceOf(
			Datastore\Error\FileAccessUnreadable::class,
			$Err
		);

		return;
	}

	#[Test]
	public function
	MethodLoad_FileFormatInvalidJSON():
	void {

		$Store = new Datastore;
		$Filename = LocalTouchTempFile('nether-datastore-readfmt', 'json');
		$Err = NULL;

		try{ $Store->Load($Filename); }
		catch(Exception $Err) { }

		unlink($Filename);

		$this->assertInstanceOf(
			Datastore\Error\FileFormatInvalid::class,
			$Err
		);

		return;
	}

	#[Test]
	public function
	MethodLoad_FileFormatInvalidJSON2():
	void {

		$Store = new Datastore;
		$Filename = LocalTouchTempFile('nether-datastore-readfmt', 'json');
		$Err = NULL;

		file_put_contents($Filename, 'shiggity shiggity shwa');

		try{ $Store->Load($Filename); }
		catch(Exception $Err) { }

		unlink($Filename);

		$this->assertInstanceOf(
			Datastore\Error\FileFormatInvalid::class,
			$Err
		);

		return;
	}

	#[Test]
	public function
	MethodLoad_FileFormatInvalidPHSON():
	void {

		$Store = new Datastore;
		$Filename = LocalTouchTempFile('nether-datastore-readfmt', 'phson');
		$Err = NULL;

		try{ $Store->Load($Filename); }
		catch(Exception $Err) { }

		unlink($Filename);

		$this->assertInstanceOf(
			Datastore\Error\FileFormatInvalid::class,
			$Err
		);

		return;
	}

	#[Test]
	public function
	MethodLoad_FileFormatInvalidPHSON2():
	void {

		$Store = new Datastore;
		$Filename = LocalTouchTempFile('nether-datastore-readfmt', 'phson');
		$Err = NULL;

		file_put_contents($Filename, 'shiggity shiggity shwa');

		try{ $Store->Load($Filename); }
		catch(Exception $Err) { }

		unlink($Filename);

		$this->assertInstanceOf(
			Datastore\Error\FileFormatInvalid::class,
			$Err
		);

		return;
	}

	#[Test]
	public function
	MethodWrite_DirectoryUnwritable():
	void {

		$Store = new Datastore;
		$Err = NULL;

		$Filename = sprintf(
			'%s%sasdfasdfasdf%sasdfasdfadsf.lol',
			LocalTempDir(),
			DIRECTORY_SEPARATOR,
			DIRECTORY_SEPARATOR
		);

		// fail to write because the parent directory does not exist.

		try { $Store->Write($Filename); }
		catch(Exception $Err) { }

		$this->assertInstanceOf(
			Datastore\Error\FileAccessUnwritable::class,
			$Err
		);

		return;
	}

	#[Test]
	public function
	MethodWrite_FileUnwritable():
	void {

		$Store = new Datastore;
		$Err = NULL;

		$Filename = LocalTouchTempFile('nether-datastore-writelocked', 'json');
		LocalLockFile($Filename);

		// fail to write because the file is unwritable.

		try { $Store->Write($Filename); }
		catch(Exception $Err) { }

		LocalUnlockFile($Filename);
		unlink($Filename);

		$this->assertInstanceOf(
			Datastore\Error\FileAccessUnwritable::class,
			$Err
		);

		return;
	}

	#[Test]
	public function
	MethodWriteJSON():
	void {

		$Err = NULL;
		$Store = new Datastore(static::DatasetSimpleMap);
		$Other = new Datastore;
		$Filename = LocalTouchTempFile('nether-datastore-writejson', 'json');
		$Expect = json_encode($Store->Export());

		// test writing a file to disk.

		$Store->Write($Filename);
		$this->assertTrue(file_exists($Filename));
		$this->assertGreaterThan(1, filesize($Filename));
		$this->assertEquals($Expect, file_get_contents($Filename));

		// test loading that file from disk.

		$Other->Load($Filename);
		$this->assertCount(static::DatasetSimpleCount, $Other);
		$this->assertEquals(3, $Other->Get('three'));-

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
		$Filename = LocalTouchTempFile('nether-datastore-writephson', 'phson');
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
	MethodIsEmpty():
	void {

		$Store = new Datastore(static::DatasetSimpleList);
		$this->assertFalse($Store->IsEmpty());

		$Store->Import([]);
		$this->assertTrue($Store->IsEmpty());

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
	MethodClear():
	void {

		$Store = new Datastore(static::DatasetSimpleList);
		$this->assertEquals(static::DatasetSimpleCount, $Store->Count());

		$Store->Clear();
		$this->assertEquals(0, $Store->Count());

		return;
	}

	#[Test]
	public function
	MethodCopy():
	void {

		$Store = new Datastore(static::DatasetSimpleList);
		$Fresh = $Store->Copy();

		$this->assertNotEquals(
			spl_object_id($Store),
			spl_object_id($Fresh)
		);

		$Store->Clear();
		$this->assertEquals(0, $Store->Count());
		$this->assertEquals(static::DatasetSimpleCount, $Fresh->Count());

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
	MethodShuffle():
	void {

		$Store = new Datastore(static::DatasetSimpleList);
		$Last = json_encode($Store->Export());
		$Shuffed = NULL;
		$Tries = 500;
		$ComeOnNow = 0;

		for($Loop = 0; $Loop < $Tries; $Loop++) {
			$Store->Shuffle();
			$Shuffed = json_encode($Store->Export());

			if($Shuffed === $Last)			
			$ComeOnNow++;

			$Last = $Shuffed;
			continue;
		}

		$this->assertNotEquals(
			$Tries, $ComeOnNow,
			'seeing this means you should buy a lottery ticket. or it might be fucked, idk.'
		);

		return;
	}

	#[Test]
	public function
	MethodSortByKey():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);

		$Store->SortByKey();
		$Store->Reindex();

		$this->assertEquals(1, $Store->Get(0));
		$this->assertEquals(3, $Store->Get(1));
		$this->assertEquals(2, $Store->Get(2));

		return;
	}

	#[Test]
	public function
	MethodSortByValue():
	void {

		$Store = new Datastore(
			array_reverse(static::DatasetSimpleMap, TRUE)
		);

		$Store->SortByValue();
		$Store->Reindex();

		$this->assertEquals(1, $Store->Get(0));
		$this->assertEquals(2, $Store->Get(1));
		$this->assertEquals(3, $Store->Get(2));

		return;
	}

	#[Test]
	public function
	MethodSortByKeyCallable():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);

		$Store->SortByKey(function(mixed $A, mixed $B) {
			return $A <=> $B;
		});

		$Store->Reindex();

		$this->assertEquals(1, $Store->Get(0));
		$this->assertEquals(3, $Store->Get(1));
		$this->assertEquals(2, $Store->Get(2));

		return;
	}

	#[Test]
	public function
	MethodSortByValueCallable():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);

		$Store->SortByValue(function(mixed $A, mixed $B) {
			return $B <=> $A;
		});

		$Store->Reindex();

		$this->assertEquals(3, $Store->Get(0));
		$this->assertEquals(2, $Store->Get(1));
		$this->assertEquals(1, $Store->Get(2));

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

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
	MethodKeys():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Keys = $Store->GetKeys();

		$this->assertInstanceOf(Datastore::class, $Keys);
		$this->assertEquals('one', $Keys[0]);
		$this->assertEquals('two', $Keys[1]);
		$this->assertEquals('three', $Keys[2]);

		return;
	}

	#[Test]
	public function
	MethodValues():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Vals = $Store->GetValues();

		$this->assertInstanceOf(Datastore::class, $Vals);
		$this->assertEquals(1, $Vals[0]);
		$this->assertEquals(2, $Vals[1]);
		$this->assertEquals(3, $Vals[2]);

		return;
	}

	#[Test]
	public function
	MethodFirstKeyAndValue():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);

		$this->assertEquals('one', $Store->GetFirstKey());

		$this->assertEquals(1, $Store->GetFirstValue());

		$this->assertTrue($Store->IsFirstKey('one'));

		$this->assertFalse($Store->IsFirstKey('two'));

		return;
	}

	#[Test]
	public function
	MethodLastKeyAndValue():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);

		$this->assertEquals('three', $Store->GetLastKey());

		$this->assertEquals(3, $Store->GetLastValue());

		$this->assertTrue($Store->IsLastKey('three'));

		$this->assertFalse($Store->IsLastKey('two'));

		return;
	}

	#[Test]
	public function
	MethodJoin():
	void {

		$Store = new Datastore(static::DatasetSimpleList);
		$Result = $Store->Join(', ');

		$this->assertEquals('one, two, three', $Result);

		return;
	}

	#[Test]
	public function
	MethodMerge():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Store->Merge(static::DatasetSimpleMapOverlap);

		// 3 should have been overwritten.

		$this->assertEquals(5, $Store->Count());
		$this->assertEquals(2, $Store['two']);
		$this->assertEquals(-3, $Store['three']);
		$this->assertEquals(4, $Store['four']);

		// expect: one two three four five

		$this->assertEquals('one', $Store->GetFirstKey());
		$this->assertEquals('five', $Store->GetLastKey());

		return;
	}

	#[Test]
	public function
	MethodMergeIterable():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Store->Merge(new ArrayIterator(static::DatasetSimpleMapOverlap));

		// 3 should have been overwritten.

		$this->assertEquals(5, $Store->Count());
		$this->assertEquals(2, $Store['two']);
		$this->assertEquals(-3, $Store['three']);
		$this->assertEquals(4, $Store['four']);

		// expect: one two three four five

		$this->assertEquals('one', $Store->GetFirstKey());
		$this->assertEquals('five', $Store->GetLastKey());

		return;
	}

	#[Test]
	public function
	MethodMergeStart():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Store->MergeStart(static::DatasetSimpleMapOverlap);

		// 3 should have been overwritten.

		$this->assertEquals(5, $Store->Count());
		$this->assertEquals(2, $Store['two']);
		$this->assertEquals(-3, $Store['three']);
		$this->assertEquals(4, $Store['four']);

		// expect: four five one two three

		$this->assertEquals('four', $Store->GetFirstKey());
		$this->assertEquals('three', $Store->GetLastKey());

		return;
	}

	#[Test]
	public function
	MethodMergeStartIterable():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Store->MergeStart(new ArrayIterator(static::DatasetSimpleMapOverlap));

		// 3 should have been overwritten.

		$this->assertEquals(5, $Store->Count());
		$this->assertEquals(2, $Store['two']);
		$this->assertEquals(-3, $Store['three']);
		$this->assertEquals(4, $Store['four']);

		// expect: four five one two three

		$this->assertEquals('four', $Store->GetFirstKey());
		$this->assertEquals('three', $Store->GetLastKey());

		return;
	}

	#[Test]
	public function
	MethodBlend():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Store->Blend(static::DatasetSimpleMapOverlap);

		// 3 should not have been overwritten.

		$this->assertEquals(5, $Store->Count());
		$this->assertEquals(2, $Store['two']);
		$this->assertEquals(3, $Store['three']);
		$this->assertEquals(4, $Store['four']);

		// expect: one two three four five

		$this->assertEquals('one', $Store->GetFirstKey());
		$this->assertEquals('five', $Store->GetLastKey());

		return;
	}

	#[Test]
	public function
	MethodBlendIterable():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Store->Blend(new ArrayIterator(static::DatasetSimpleMapOverlap));

		// 3 should not have been overwritten.

		$this->assertEquals(5, $Store->Count());
		$this->assertEquals(2, $Store['two']);
		$this->assertEquals(3, $Store['three']);
		$this->assertEquals(4, $Store['four']);

		// expect: one two three four five

		$this->assertEquals('one', $Store->GetFirstKey());
		$this->assertEquals('five', $Store->GetLastKey());

		return;
	}

	#[Test]
	public function
	MethodBlendStart():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Store->BlendStart(static::DatasetSimpleMapOverlap);

		// 3 should not have been overwritten.
		
		$this->assertEquals(5, $Store->Count());
		$this->assertEquals(2, $Store['two']);
		$this->assertEquals(3, $Store['three']);
		$this->assertEquals(4, $Store['four']);

		// expect: four five one two three

		$this->assertEquals('four', $Store->GetFirstKey());
		$this->assertEquals('three', $Store->GetLastKey());

		return;
	}

	#[Test]
	public function
	MethodBlendStartIterable():
	void {

		$Store = new Datastore(static::DatasetSimpleMap);
		$Store->BlendStart(new ArrayIterator(static::DatasetSimpleMapOverlap));

		// 3 should not have been overwritten.
		
		$this->assertEquals(5, $Store->Count());
		$this->assertEquals(2, $Store['two']);
		$this->assertEquals(3, $Store['three']);
		$this->assertEquals(4, $Store['four']);

		// expect: four five one two three

		$this->assertEquals('four', $Store->GetFirstKey());
		$this->assertEquals('three', $Store->GetLastKey());

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
		$Store->Filter(fn($V)=> $V === 'two')->Import(
			array_values($Store->Export())
		);

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
		$Other = $Store->Distill(fn($V)=> $V === 'two')->Reindex();

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

	#[Test]
	public function
	MethodReindex():
	void {

		$Map = new Datastore(static::DatasetSimpleMap);
		$this->assertFalse($Map->IsList());

		$Map->Reindex();
		$this->assertTrue($Map->IsList());

		return;
	}

	#[Test]
	public function
	MethodReindexKeyValue():
	void {

		$Err = NULL;

		$Map = new Datastore([
			'a' => 1,
			'b' => 2,
			'c' => 3,
			'd' => 4
		]);

		$Map->ReindexKeyValue(function(mixed $K, mixed $V) {
			if($K === 'a') return FALSE;
			if($K === 'b') return NULL;
			if($K === 'c') return 'C';
			if($K === 'd') return [ 'D', 'DEEEE' ];
		});

		$this->assertEquals(3, $Map->Count());

		$this->assertFalse($Map->Has('a'));
		$this->assertTrue($Map->Has('b'));
		$this->assertTrue($Map->Has('C'));
		$this->assertTrue($Map->Has('D'));

		$this->assertEquals(2, $Map->Get('b'));
		$this->assertEquals(3, $Map->Get('C'));
		$this->assertEquals('DEEEE', $Map->Get('D'));

		////////

		try {
			$Map->ReindexKeyvalue(function(mixed $K, mixed $V) {
				return [ 69 ];
			});
		}

		catch(Nether\Datastore\Error\ReindexFormatInvalid $Err) { }

		$this->assertInstanceOf(
			Nether\Datastore\Error\ReindexFormatInvalid::class,
			$Err
		);

		////////

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
