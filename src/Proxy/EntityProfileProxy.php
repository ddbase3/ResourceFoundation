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
use ResourceFoundation\Api\IEntityProfileService;

class EntityProfileProxy implements IEntityProfileService {

	public function __construct(
		private readonly IMicroserviceConnector $entityProfileService
	) {}


	public function getActiveProfile(?int $userId = null): ?array {
		return $this->entityProfileService->getActiveProfile($userId);
	}


	public function getProfiles(?int $userId = null, bool $includeArchived = false): array {
		return $this->entityProfileService->getProfiles($userId, $includeArchived) ?? [];
	}


	public function createProfile(int $userId, array $profile): int|string {
		return $this->entityProfileService->createProfile($userId, $profile) ?? 0;
	}


	public function updateProfile(int|string $profileId, array $patch): void {
		$this->entityProfileService->updateProfile($profileId, $patch);
	}


	public function archiveProfile(int|string $profileId): void {
		$this->entityProfileService->archiveProfile($profileId);
	}


	public function setActiveProfile(int $userId, int $profileId): void {
		$this->entityProfileService->setActiveProfile($userId, $profileId);
	}
}
