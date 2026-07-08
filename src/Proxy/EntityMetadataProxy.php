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
use ResourceFoundation\Api\IEntityMetadataService;

class EntityMetadataProxy implements IEntityMetadataService {

	public function __construct(
		private readonly IMicroserviceConnector $entityMetadataService
	) {}


	public function getMetadata(int|string $entryId): array {
		return $this->entityMetadataService->getMetadata($entryId) ?? [];
	}


	public function getMetadataValue(int|string $entryId, string $name, mixed $default = null): mixed {
		return $this->entityMetadataService->getMetadataValue($entryId, $name, $default);
	}


	public function setMetadata(int|string $entryId, array $metadata): void {
		$this->entityMetadataService->setMetadata($entryId, $metadata);
	}


	public function removeMetadata(int|string $entryId, array $names): void {
		$this->entityMetadataService->removeMetadata($entryId, $names);
	}


	public function replaceMetadata(int|string $entryId, array $metadata): void {
		$this->entityMetadataService->replaceMetadata($entryId, $metadata);
	}
}
