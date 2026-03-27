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

class TableMetadata {

	public function __construct(
		public string $name,
		public ?string $label = null,
		public ?string $description = null,
		public string $domain = '',
		public string $category = '',
		public array $tags = [],
		public array $fields = [],           // FieldMetadata[]
		public array $joins = [],            // JoinMetadata[]
		public array $defaultFilters = [],   // FilterCondition[]
		public bool $sensitive = false,
		public array $position = []
	) {}
}
