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
