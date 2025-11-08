<?php declare(strict_types=1);

namespace ResourceFoundation\Api;

use ResourceFoundation\Dto\QueryResult;
use ResourceFoundation\Dto\TableMetadata;

/**
 * Generic query service interface providing access to data tables,
 * metadata and structured query execution.
 */
interface IQueryService {

	/**
	 * Returns the list of all visible tables for the current user.
	 *
	 * @return TableMetadata[]
	 */
	public function listTables(): array;

	/**
	 * Returns metadata of a specific table if accessible by the current user.
	 *
	 * @param string $tableName
	 * @return TableMetadata|null
	 */
	public function getTable(string $tableName): ?TableMetadata;

	/**
	 * Executes a structured JSON-based query and returns the result
	 * if the current user has permission to access the requested data.
	 *
	 * @param array $queryJson Structured query definition
	 * @return QueryResult Query result data
	 *
	 * @throws \ResourceFoundation\Exception\AccessDeniedException
	 * @throws \ResourceFoundation\Exception\QueryValidationException
	 */
	public function executeQuery(array $queryJson): QueryResult;

	/**
	 * Returns all known domains visible to the current user.
	 *
	 * @return string[]
	 */
	public function listDomains(): array;

	/**
	 * Returns all known categories visible to the current user.
	 *
	 * @return string[]
	 */
	public function listCategories(): array;

	/**
	 * Returns all tags used across visible tables and fields.
	 *
	 * @return string[]
	 */
	public function listTags(): array;
}
