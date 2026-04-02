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
 * Generic interface for managing file entities.
 * A file entity combines typed entry data (filename, tmpname, mime, size, etc.)
 * with the physical file content stored through an IFileStorage backend.
 */
interface IEntityFileService {

	/**
	 * Creates a new file entry and stores the physical file.
	 *
	 * Expected file payload keys:
	 * - filename (required)
	 * - content_base64 (required)
	 * - mime (optional)
	 * - size (optional)
	 * - name (optional, defaults to filename)
	 * - description (optional)
	 * - content (optional, e.g. caption)
	 * - preview (optional)
	 *
	 * Supported options:
	 * - allocs / alloc
	 * - tags / tag
	 * - metadata
	 * - useraccess
	 * - groupaccess
	 *
	 * @param array $file Normalized file payload
	 * @param array $options Create options for the related entity
	 * @return array Created file entry including loaded typed data
	 */
	public function createFile(array $file, array $options = []): array;

	/**
	 * Replaces the physical content and metadata of an existing file entry.
	 * The existing tmpname/path should remain stable whenever possible.
	 *
	 * Expected file payload keys:
	 * - content_base64 (required)
	 * - filename (optional, defaults to current filename)
	 * - mime (optional)
	 * - size (optional)
	 * - name (optional, defaults to filename)
	 * - description (optional)
	 * - content (optional, e.g. caption)
	 * - preview (optional)
	 *
	 * @param int|string $id File entry identifier
	 * @param array $file Normalized replacement payload
	 * @param array $options Reserved for future replace options
	 * @return array Updated file entry including loaded typed data
	 */
	public function replaceFile(int|string $id, array $file, array $options = []): array;

	/**
	 * Returns a single file entry by its identifier.
	 *
	 * The result is the normal entity representation with typed file payload
	 * in the "data" field.
	 *
	 * @param int|string $id File entry identifier
	 * @param array $options Load options
	 * @return array|null File entry or null if not found
	 */
	public function getFile(int|string $id, array $options = []): ?array;

	/**
	 * Returns the physical file content for a file entry.
	 *
	 * Supported options:
	 * - encoding => "base64" (default) or "raw"
	 *
	 * @param int|string $id File entry identifier
	 * @param array $options Content loading options
	 * @return string|null Encoded content or null if not found
	 */
	public function getFileContent(int|string $id, array $options = []): ?string;

	/**
	 * Deletes a file entry and optionally the physical file.
	 *
	 * Supported options:
	 * - deletephysical => bool (default true)
	 *
	 * @param int|string $id File entry identifier
	 * @param array $options Delete options
	 * @return bool True on success, false otherwise
	 */
	public function deleteFile(int|string $id, array $options = []): bool;
}
