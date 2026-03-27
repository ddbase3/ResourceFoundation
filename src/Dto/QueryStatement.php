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

/**
 * Represents a compiled SQL query including raw SQL,
 * bound parameters, selected field metadata, and sensitivity flag.
 */
class QueryStatement {

	/**
	 * @param string $sql              The compiled SQL statement.
	 * @param array $params            Bound parameters for the SQL query.
	 * @param array $fields            Metadata for selected fields (name, alias, table, etc.).
	 * @param bool $sensitive          True if query touches sensitive data.
	 * @param bool $isWildcardQuery    True if query includes wildcard fields (e.g. table.* or *).
	 */
	public function __construct(
		public string $sql,
		public array $params = [],
		public array $fields = [],
		public bool $sensitive = false,
		public bool $isWildcardQuery = false
	) {}
}

