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
use ResourceFoundation\Api\IEntityTagService;

class EntityTagProxy implements IEntityTagService {

	public function __construct(
		private readonly IMicroserviceConnector $entityTagService
	) {}


	public function getEntryTags(int|string $entryId): array {
		return $this->entityTagService->getEntryTags($entryId) ?? [];
	}


	public function addEntryTags(int|string $entryId, array $tags): void {
		$this->entityTagService->addEntryTags($entryId, $tags);
	}


	public function removeEntryTags(int|string $entryId, array $tags): void {
		$this->entityTagService->removeEntryTags($entryId, $tags);
	}


	public function replaceEntryTags(int|string $entryId, array $tags): void {
		$this->entityTagService->replaceEntryTags($entryId, $tags);
	}


	public function getTags(?string $scope = null): array {
		return $this->entityTagService->getTags($scope) ?? [];
	}


	public function describeTag(string $tag, string $description): void {
		$this->entityTagService->describeTag($tag, $description);
	}


	public function assignTagToScope(string $tag, string $scope): void {
		$this->entityTagService->assignTagToScope($tag, $scope);
	}


	public function removeTagFromScope(string $tag, string $scope): void {
		$this->entityTagService->removeTagFromScope($tag, $scope);
	}


	public function assignTagToModule(string $tag, string $module): void {
		$this->entityTagService->assignTagToModule($tag, $module);
	}


	public function removeTagFromModule(string $tag, string $module): void {
		$this->entityTagService->removeTagFromModule($tag, $module);
	}
}
