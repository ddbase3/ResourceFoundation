<?php declare(strict_types=1);

namespace ResourceApi\Api;

/**
 * IFileStorage defines a generic interface for accessing files and directories.
 * Implementations may represent local file systems, WebDAV, S3, FTP, or other backends.
 */
interface IFileStorage {

	/**
	 * List files and directories within the given path.
	 * @param string $path Relative or absolute directory path
	 * @return array List of items (each may include name, type, size, modified, etc.)
	 */
	public function list(string $path = ''): array;

	/**
	 * Read the content of a file.
	 * @param string $path Path to the file
	 * @return string File content
	 */
	public function read(string $path): string;

	/**
	 * Write content to a file (create or overwrite).
	 * @param string $path Path to the file
	 * @param string $content File content to write
	 * @return bool True on success, false otherwise
	 */
	public function write(string $path, string $content): bool;

	/**
	 * Delete a file.
	 * @param string $path Path to the file
	 * @return bool True on success, false otherwise
	 */
	public function delete(string $path): bool;

	/**
	 * Create a new directory.
	 * @param string $path Path of the directory to create
	 * @return bool True on success, false otherwise
	 */
	public function mkdir(string $path): bool;

	/**
	 * Remove a directory (optionally recursive, depending on implementation).
	 * @param string $path Path of the directory to remove
	 * @return bool True on success, false otherwise
	 */
	public function rmdir(string $path): bool;

	/**
	 * Check if a file or directory exists.
	 * @param string $path Path to check
	 * @return bool True if exists, false otherwise
	 */
	public function exists(string $path): bool;

	/**
	 * Retrieve metadata for a file or directory.
	 * @param string $path Path to the file or directory
	 * @return array|null Associative array with metadata or null if not found
	 */
	public function stat(string $path): ?array;
}

