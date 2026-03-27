<?php declare(strict_types=1);

/***********************************************************************
 * This file is part of ResourceFoundation for BASE3 Framework.
 *
 * ResourceFoundation extends the BASE3 framework with a unified API
 * foundation for resource access, entity management, and file storage.
 * It provides shared interfaces for extensible data backends.
 *
 * Developed by Daniel Dahme
 * Licensed under GPL-3.0
 * https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * https://base3.de/v/resourcefoundation
 * https://github.com/ddbase3/ResourceFoundation
 **********************************************************************/

namespace ResourceFoundation\Proxy;

use Base3\Microservice\Api\IMicroserviceConnector;
use ResourceFoundation\Api\IEntityDataService;

class EntityDataProxy implements IEntityDataService {

	public function __construct(
		private readonly IMicroserviceConnector $entityDataService
	) {}

	// Implementation of IEntityDataService

	public function getEntries(array $options = []): array {
		return $this->entityDataService->getEntries($options) ?? [];
	}

	public function getEntry(int|string $id, array $options = []): ?array {
		return $this->entityDataService->getEntry($id, $options);
	}

	public function createEntry(array $data): int|string {
		return $this->entityDataService->createEntry($data) ?? 0;
	}

	public function updateEntry(int|string $id, array $patch): int|string {
		return $this->entityDataService->updateEntry($id, $patch) ?? 0;
	}

	public function deleteEntry(int|string $id): bool {
		return $this->entityDataService->deleteEntry($id) ?? false;
	}
}
