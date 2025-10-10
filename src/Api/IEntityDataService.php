<?php declare(strict_types=1);

namespace ResourceFoundation\Api;

/**
 * Generic interface for accessing and managing data entities.
 * Provides basic CRUD operations and supports optional loading parameters
 * for partial or lazy data retrieval.
 */
interface IEntityDataService {

	/**
	 * Returns a list of entities based on the given options.
	 * Options may include filters, sorting, or loading instructions.
	 *
	 * @param array $options Query or loading options
	 * @return array List of entities as associative arrays
	 */
	public function getEntries(array $options = []): array;

	/**
	 * Returns a single entity by its identifier.
	 * Can include options for lazy-loading related data (e.g. tags, relations).
	 *
	 * @param int|string $id Entity identifier
	 * @param array $options Loading options
	 * @return array|null Entity data or null if not found
	 */
	public function getEntry(int|string $id, array $options = []): ?array;

	/**
	 * Creates or updates an entity based on the provided data.
	 * Implementations should detect whether to insert or update.
	 *
	 * @param array $data Entity data to save
	 * @return int|string ID of the saved entity
	 */
	public function saveEntry(array $data): int|string;

	/**
	 * Deletes an entity by its identifier.
	 *
	 * @param int|string $id Entity identifier
	 * @return bool True on success, false otherwise
	 */
	public function deleteEntry(int|string $id): bool;
}

