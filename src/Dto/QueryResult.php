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

namespace ResourceFoundation\Dto;

class QueryResult {

	/**
	 * @param array $columns Array of column metadata, e.g.:
	 *   [
	 *     [
	 *       'name' => string,      // preferred display name (alias or field)
	 *       'type' => string,      // PHP type from result set
	 *       'field' => ?string,    // original field name (optional)
	 *       'alias' => ?string,    // SQL alias if used
	 *       'table' => ?string     // table source if known
	 *       'sensitive' => bool    // contains sensitive data
	 *     ],
	 *     ...
	 *   ]
	 * @param array $rows Array of result rows: associative arrays
	 * @param string|null $debugSql Optional debug SQL string
	 * @param bool $sensitive Result contains sensitive data
	 * @param int|null $affectedRows Affected rows for write queries (INSERT/UPDATE/DELETE)
	 * @param int|string|null $insertId Insert ID for INSERT queries (backend-dependent)
	 */
	public function __construct(
		public array $columns,
		public array $rows,
		public ?string $debugSql = null,
		public bool $sensitive = false,
		public ?int $affectedRows = null,
		public int|string|null $insertId = null
	) {}
}
