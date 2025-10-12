<?php declare(strict_types=1);

namespace ResourceFoundation\Proxy;

use Base3\Microservice\Api\IMicroserviceConnector;
use ResourceFoundation\Api\IEntityDataService;

class EntityDataProxy implements IEntityDataService {

	public function __construct(
		private readonly IMicroserviceConnector $entityDataService
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
}
