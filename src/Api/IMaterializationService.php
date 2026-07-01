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

use ResourceFoundation\Dto\MaterializationRunResult;

interface IMaterializationService {

	public function buildFull(string $manifestId): MaterializationRunResult;

	public function buildIncremental(string $manifestId): MaterializationRunResult;

	public function refresh(string $manifestId): MaterializationRunResult;
}
