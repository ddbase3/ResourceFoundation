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
 * Provides metadata management for entities.
 *
 * Metadata is entry-wide key-value data. Implementations may store scalar values
 * directly and structured values as JSON or another backend-specific encoding.
 */
interface IEntityMetadataService {

	/**
	 * Returns all metadata for an entity.
	 *
	 * @param int|string $entryId Entity identifier
	 * @return array<string,mixed> Metadata keyed by name
	 */
	public function getMetadata(int|string $entryId): array;

	/**
	 * Returns a single metadata value.
	 *
	 * @param int|string $entryId Entity identifier
	 * @param string $name Metadata key
	 * @param mixed $default Default value when missing
	 * @return mixed Metadata value or default
	 */
	public function getMetadataValue(int|string $entryId, string $name, mixed $default = null): mixed;

	/**
	 * Sets one or more metadata values while preserving other keys.
	 *
	 * @param int|string $entryId Entity identifier
	 * @param array<string,mixed> $metadata Metadata values to set
	 */
	public function setMetadata(int|string $entryId, array $metadata): void;

	/**
	 * Removes metadata keys.
	 *
	 * @param int|string $entryId Entity identifier
	 * @param array<int,string> $names Metadata keys to remove
	 */
	public function removeMetadata(int|string $entryId, array $names): void;

	/**
	 * Replaces the complete metadata set for an entity.
	 *
	 * @param int|string $entryId Entity identifier
	 * @param array<string,mixed> $metadata Complete metadata set
	 */
	public function replaceMetadata(int|string $entryId, array $metadata): void;
}
