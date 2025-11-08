<?php declare(strict_types=1);

namespace ResourceFoundation\Api;

use ResourceFoundation\Dto\TableMetadata;

/**
 * Generic interface for providing queryable schema metadata.
 */
interface IQuerySchemaProvider {

	/**
	 * Returns the full schema definition visible to the current context.
	 *
	 * @return TableMetadata[] List of all available table definitions
	 */
	public function getSchema(): array;

	/**
	 * Returns a single table definition by name, or null if not found.
	 *
	 * @param string $tableName Table identifier
	 * @return TableMetadata|null Table metadata or null if unavailable
	 */
	public function getTable(string $tableName): ?TableMetadata;
}
