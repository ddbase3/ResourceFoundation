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

namespace ResourceFoundation\Api;

/**
 * Provides log and comment access for entities.
 */
interface IEntityActivityService {

	/**
	 * Returns log rows for an entity.
	 *
	 * @param int|string $entryId Entity identifier
	 * @param array<string,mixed> $options Backend-specific options such as limit or reverse
	 * @return array<int,array<string,mixed>> Log rows
	 */
	public function getLogs(int|string $entryId, array $options = []): array;

	/** Adds one log entry for an entity. */
	public function addLog(int|string $entryId, string $action, ?int $userId = null): void;

	/**
	 * Returns comment rows for an entity.
	 *
	 * @param int|string $entryId Entity identifier
	 * @param array<string,mixed> $options Backend-specific options such as limit or parent_id
	 * @return array<int,array<string,mixed>> Comment rows
	 */
	public function getComments(int|string $entryId, array $options = []): array;

	/** Adds one comment and returns its identifier. */
	public function addComment(int|string $entryId, string $comment, ?int $parentId = null): int|string;

	/** Updates a comment body. */
	public function updateComment(int|string $commentId, string $comment): void;

	/** Deletes a comment. */
	public function deleteComment(int|string $commentId): void;
}
