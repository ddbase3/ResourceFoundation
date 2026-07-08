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
 * Provides user-specific data attached to entities.
 *
 * User data is separate from entity-wide metadata. It is suitable for personal
 * flags, UI state, per-user markers, favorites, and similar data.
 */
interface IEntityUserDataService {

	/**
	 * Returns all user-data values for one entity/user pair.
	 *
	 * @param int|string $entryId Entity identifier
	 * @param int|null $userId User identifier, or null for the current user
	 * @return array<string,mixed> User data keyed by name
	 */
	public function getUserData(int|string $entryId, ?int $userId = null): array;

	/** Returns one user-data value. */
	public function getUserDataValue(int|string $entryId, string $name, mixed $default = null, ?int $userId = null): mixed;

	/** Sets one or more user-data values. */
	public function setUserData(int|string $entryId, array $data, ?int $userId = null): void;

	/** Removes user-data values. */
	public function removeUserData(int|string $entryId, array $names, ?int $userId = null): void;
}
