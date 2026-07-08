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
use ResourceFoundation\Api\IEntityActivityService;

class EntityActivityProxy implements IEntityActivityService {

	public function __construct(
		private readonly IMicroserviceConnector $entityActivityService
	) {}


	public function getLogs(int|string $entryId, array $options = []): array {
		return $this->entityActivityService->getLogs($entryId, $options) ?? [];
	}


	public function addLog(int|string $entryId, string $action, ?int $userId = null): void {
		$this->entityActivityService->addLog($entryId, $action, $userId);
	}


	public function getComments(int|string $entryId, array $options = []): array {
		return $this->entityActivityService->getComments($entryId, $options) ?? [];
	}


	public function addComment(int|string $entryId, string $comment, ?int $parentId = null): int|string {
		return $this->entityActivityService->addComment($entryId, $comment, $parentId) ?? 0;
	}


	public function updateComment(int|string $commentId, string $comment): void {
		$this->entityActivityService->updateComment($commentId, $comment);
	}


	public function deleteComment(int|string $commentId): void {
		$this->entityActivityService->deleteComment($commentId);
	}
}
