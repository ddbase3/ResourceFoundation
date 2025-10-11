<?php declare(strict_types=1);

namespace ResourceFoundation\Proxy;

use Base3\Api\IProxy;
use Base3\Api\IConnector;
use Base3\Microservice\AbstractMicroservice;
use ResourceFoundation\Api\IEntityDataService;

class EntityDataProxy extends AbstractMicroservice implements IEntityDataService, IProxy {

	public function __construct(
		private readonly IEntityDataService|IConnector $entityDataService
	) {}

	// Implementation of IEntityDataService

	public function getEntries(array $options = []): array {
		return $this->entityDataService->getEntries($options);
	}

	public function getEntry(int|string $id, array $options = []): ?array {
		return $this->entityDataService->getEntry($id, $options);
	}

	public function saveEntry(array $data): int|string {
		return $this->entityDataService->saveEntry($data);
	}

	public function deleteEntry(int|string $id): bool {
		return $this->entityDataService->deleteEntry($id);
	}

	// Implementation of IProxy

	public function getProxiedInstance(): object {
		return $this->entityDataService;
	}
}
