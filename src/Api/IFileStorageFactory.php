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

/**
 * Creates context-specific file storage instances.
 *
 * The storage identifier and mode are implementation-specific. Their lifecycle
 * remains outside the factory so callers can persist them in their own domain.
 */
interface IFileStorageFactory {

	/**
	 * Open an existing logical storage.
	 *
	 * @param string $id Implementation-specific storage identifier
	 * @param string $mode Implementation-specific storage mode
	 */
	public function openStorage(string $id, string $mode): IFileStorage;
}
