<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Proxy;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Proxy\EntityDataProxy;
use Base3\Microservice\Api\IMicroserviceConnector;

class FakeEntityDataConnector implements IMicroserviceConnector {

	public ?array $entriesResult = null;
	public ?array $entryResult = null;
	public int|string|null $createResult = null;
	public int|string|null $updateResult = null;
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

	public function createEntry(array $data): int|string|null {
		$this->lastCall = 'createEntry';
		$this->lastArgs = [$data];
		return $this->createResult;
	}

	public function updateEntry(int|string $id, array $patch): int|string|null {
		$this->lastCall = 'updateEntry';
		$this->lastArgs = [$id, $patch];
		return $this->updateResult;
	}

	public function deleteEntry(int|string $id): ?bool {
		$this->lastCall = 'deleteEntry';
		$this->lastArgs = [$id];
		return $this->deleteResult;
	}
}
