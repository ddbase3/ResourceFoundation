<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Proxy;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Proxy\EntityDataProxy;
use Base3\Microservice\Api\IMicroserviceConnector;

class EntityDataProxyTest extends TestCase {

	public function testGetEntriesDelegatesAndFallsBackToEmptyArray(): void {
		$conn = new FakeEntityDataConnector();
		$proxy = new EntityDataProxy($conn);

		$conn->entriesResult = null;
		$out = $proxy->getEntries(['limit' => 10]);

		$this->assertSame('getEntries', $conn->lastCall);
		$this->assertSame([['limit' => 10]], $conn->lastArgs);
		$this->assertSame([], $out);

		$conn->entriesResult = [['id' => 1], ['id' => 2]];
		$out = $proxy->getEntries();

		$this->assertSame([['id' => 1], ['id' => 2]], $out);
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

	public function testSaveEntryDelegatesAndFallsBackToZero(): void {
		$conn = new FakeEntityDataConnector();
		$proxy = new EntityDataProxy($conn);

		$conn->saveResult = null;
		$out = $proxy->saveEntry(['name' => 'x']);

		$this->assertSame('saveEntry', $conn->lastCall);
		$this->assertSame([['name' => 'x']], $conn->lastArgs);
		$this->assertSame(0, $out);

		$conn->saveResult = 42;
		$out = $proxy->saveEntry(['name' => 'y']);

		$this->assertSame(42, $out);
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

class FakeEntityDataConnector implements IMicroserviceConnector {

	public ?array $entriesResult = null;
	public ?array $entryResult = null;
	public int|string|null $saveResult = null;
	public ?bool $deleteResult = null;

	public ?string $lastCall = null;
	public array $lastArgs = [];

	public function getMicroserviceUrl() {
		return 'fake://entity-data';
	}

	public function getEntries(array $options = []): ?array {
		$this->lastCall = 'getEntries';
		$this->lastArgs = [$options];
		return $this->entriesResult;
	}

	public function getEntry(int|string $id, array $options = []): ?array {
		$this->lastCall = 'getEntry';
		$this->lastArgs = [$id, $options];
		return $this->entryResult;
	}

	public function saveEntry(array $data): int|string|null {
		$this->lastCall = 'saveEntry';
		$this->lastArgs = [$data];
		return $this->saveResult;
	}

	public function deleteEntry(int|string $id): ?bool {
		$this->lastCall = 'deleteEntry';
		$this->lastArgs = [$id];
		return $this->deleteResult;
	}

}
