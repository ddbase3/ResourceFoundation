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
 * Provides user-specific entity profile management.
 *
 * Entity profiles are named filter or view presets owned by a user. A profile
 * can be active, standard, protected, or archived depending on the backend.
 * Implementations should return associative arrays and keep backend-specific
 * columns as-is so existing profile UIs can work without adapter code.
 */
interface IEntityProfileService {

	/**
	 * Returns the active profile for the given user.
	 *
	 * When $userId is null, implementations may resolve the current user from
	 * the active user/session service. If no user or no active/default profile
	 * exists, null is returned.
	 *
	 * @param int|null $userId User identifier, or null for the current user
	 * @return array|null Active profile row, or null when unavailable
	 */
	public function getActiveProfile(?int $userId = null): ?array;

	/**
	 * Returns all profiles for the given user.
	 *
	 * @param int|null $userId User identifier, or null for the current user
	 * @param bool $includeArchived Whether archived profile rows should be returned
	 * @return array<int,array<string,mixed>> Profile rows
	 */
	public function getProfiles(?int $userId = null, bool $includeArchived = false): array;

	/**
	 * Creates a new profile for the given user.
	 *
	 * Expected profile keys are implementation-defined, but common keys are:
	 * - name
	 * - profile
	 * - standard
	 * - protected
	 * - active
	 * - archive
	 *
	 * @param int $userId User identifier
	 * @param array<string,mixed> $profile Profile data
	 * @return int|string Created profile identifier
	 */
	public function createProfile(int $userId, array $profile): int|string;

	/**
	 * Updates a profile row.
	 *
	 * Fields not present in $patch must stay unchanged. Implementations should
	 * ignore unsupported fields instead of writing arbitrary columns.
	 *
	 * @param int|string $profileId Profile identifier
	 * @param array<string,mixed> $patch Partial profile data
	 */
	public function updateProfile(int|string $profileId, array $patch): void;

	/**
	 * Archives a profile row without physically deleting it.
	 *
	 * @param int|string $profileId Profile identifier
	 */
	public function archiveProfile(int|string $profileId): void;

	/**
	 * Marks one profile as active for a user and deactivates the user's other profiles.
	 *
	 * @param int $userId User identifier
	 * @param int $profileId Profile identifier
	 */
	public function setActiveProfile(int $userId, int $profileId): void;
}
