<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Proxy;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Proxy\EntityDataProxy;
use Base3\Microservice\Api\IMicroserviceConnector;

class EntityDataProxyTest extends TestCase {

	public function testGetEntriesDelegatesSuccessfulQueries(): void {
		$conn = new FakeEntityDataConnector();
		$proxy = new EntityDataProxy($conn);

		$conn->entriesResult = [];
		$out = $proxy->getEntries(['limit' => 10]);

		$this->assertSame('getEntries', $conn->lastCall);
		$this->assertSame([['limit' => 10]], $conn->lastArgs);
		$this->assertSame([], $out);

		$conn->entriesResult = [['id' => 1], ['id' => 2]];
		$out = $proxy->getEntries();

		$this->assertSame([['id' => 1], ['id' => 2]], $out);
	}

	public function testGetEntriesRejectsMissingMicroserviceResult(): void {
		$conn = new FakeEntityDataConnector();
		$proxy = new EntityDataProxy($conn);

		$conn->entriesResult = null;
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('did not return an entries result');
		$proxy->getEntries(['type' => 'file']);
	}

	public function testGetEntryDelegates(): void {
		$conn = new FakeEntityDataConnector();
		$proxy = new EntityDataProxy($conn);

		$conn->entryResult = ['id' => 7];
		$out = $proxy->getEntry(7, ['fields' => ['id']]);

		$this->assertSame('getEntry', $conn->lastCall);
		$this->assertSame([7, ['fields' => ['id']]], $conn->lastArgs);
		$this->assertSame(['id' => 7], $out);

		$conn->entryResult = null;
		$out = $proxy->getEntry('abc');

		$this->assertNull($out);
	}

	public function testCreateEntryDelegatesAndFallsBackToZero(): void {
		$conn = new FakeEntityDataConnector();
		$proxy = new EntityDataProxy($conn);

		$conn->createResult = null;
		$out = $proxy->createEntry(['name' => 'x']);

		$this->assertSame('createEntry', $conn->lastCall);
		$this->assertSame([['name' => 'x']], $conn->lastArgs);
		$this->assertSame(0, $out);

		$conn->createResult = 42;
		$out = $proxy->createEntry(['name' => 'y']);

		$this->assertSame('createEntry', $conn->lastCall);
		$this->assertSame([['name' => 'y']], $conn->lastArgs);
		$this->assertSame(42, $out);
	}

	public function testUpdateEntryDelegatesAndFallsBackToZero(): void {
		$conn = new FakeEntityDataConnector();
		$proxy = new EntityDataProxy($conn);

		$conn->updateResult = null;
		$out = $proxy->updateEntry(7, ['name' => 'x']);

		$this->assertSame('updateEntry', $conn->lastCall);
		$this->assertSame([7, ['name' => 'x']], $conn->lastArgs);
		$this->assertSame(0, $out);

		$conn->updateResult = 99;
		$out = $proxy->updateEntry('abc', ['name' => 'y']);

		$this->assertSame('updateEntry', $conn->lastCall);
		$this->assertSame(['abc', ['name' => 'y']], $conn->lastArgs);
		$this->assertSame(99, $out);
	}

	public function testDeleteEntryDelegatesAndFallsBackToFalse(): void {
		$conn = new FakeEntityDataConnector();
		$proxy = new EntityDataProxy($conn);

		$conn->deleteResult = null;
		$out = $proxy->deleteEntry(123);

		$this->assertSame('deleteEntry', $conn->lastCall);
		$this->assertSame([123], $conn->lastArgs);
		$this->assertFalse($out);

		$conn->deleteResult = true;
		$out = $proxy->deleteEntry('abc');

		$this->assertTrue($out);
	}
}

require_once __DIR__ . '/FakeEntityDataConnector.php';
