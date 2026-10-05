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
 * Owns the lifecycle of file storages addressed by a stable logical owner.
 */
interface IManagedFileStorageService {

	public const MODE_COLLECTION = 'collection';
	public const MODE_CONTAINER = 'container';

	public function openOrCreate(string $ownerGroup, string $ownerName, string $mode): IFileStorage;

	public function has(string $ownerGroup, string $ownerName, string $mode): bool;

	public function delete(string $ownerGroup, string $ownerName, string $mode): void;

	public function copyOwner(
		string $sourceGroup,
		string $sourceName,
		string $targetGroup,
		string $targetName,
		string $mode
	): void;
}
