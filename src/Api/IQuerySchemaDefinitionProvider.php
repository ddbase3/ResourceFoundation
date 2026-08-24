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

use Base3\Api\IBase;

/**
 * Provides declarative query-schema definitions for one logical scope.
 */
interface IQuerySchemaDefinitionProvider extends IBase {

	/**
	 * Returns the logical schema scope used by DataHawk queries.
	 */
	public function getScope(): string;

	/**
	 * Returns named definition datasets.
	 *
	 * Each dataset may contain an "enabled" flag and must contain a
	 * "definition" array when enabled.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function getDefinitions(): array;
}
