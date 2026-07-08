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
use ResourceFoundation\Api\IEntityUserDataService;

class EntityUserDataProxy implements IEntityUserDataService {

	public function __construct(
		private readonly IMicroserviceConnector $entityUserDataService
	) {}


	public function getUserData(int|string $entryId, ?int $userId = null): array {
		return $this->entityUserDataService->getUserData($entryId, $userId) ?? [];
	}


	public function getUserDataValue(int|string $entryId, string $name, mixed $default = null, ?int $userId = null): mixed {
		return $this->entityUserDataService->getUserDataValue($entryId, $name, $default, $userId);
	}


	public function setUserData(int|string $entryId, array $data, ?int $userId = null): void {
		$this->entityUserDataService->setUserData($entryId, $data, $userId);
	}


	public function removeUserData(int|string $entryId, array $names, ?int $userId = null): void {
		$this->entityUserDataService->removeUserData($entryId, $names, $userId);
	}
}
