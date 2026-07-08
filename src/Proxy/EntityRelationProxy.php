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
use ResourceFoundation\Api\IEntityRelationService;

class EntityRelationProxy implements IEntityRelationService {

	public function __construct(
		private readonly IMicroserviceConnector $entityRelationService
	) {}


	public function getRelations(int|string $entryId): array {
		return $this->entityRelationService->getRelations($entryId) ?? [];
	}


	public function getRelationIds(int|string $entryId): array {
		return $this->entityRelationService->getRelationIds($entryId) ?? [];
	}


	public function addRelations(int|string $entryId, array $peerIds): void {
		$this->entityRelationService->addRelations($entryId, $peerIds);
	}


	public function removeRelations(int|string $entryId, array $peerIds): void {
		$this->entityRelationService->removeRelations($entryId, $peerIds);
	}


	public function replaceRelations(int|string $entryId, array $peerIds): void {
		$this->entityRelationService->replaceRelations($entryId, $peerIds);
	}
}
