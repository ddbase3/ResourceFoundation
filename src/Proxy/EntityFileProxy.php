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

namespace ResourceFoundation\Proxy;

use Base3\Microservice\Api\IMicroserviceConnector;
use ResourceFoundation\Api\IEntityFileService;

class EntityFileProxy implements IEntityFileService {

	public function __construct(
		private readonly IMicroserviceConnector $entityFileService
	) {}

	public function createFile(array $file, array $options = []): array {
		return $this->entityFileService->createFile($file, $options) ?? [];
	}

	public function replaceFile(int|string $id, array $file, array $options = []): array {
		return $this->entityFileService->replaceFile($id, $file, $options) ?? [];
	}

	public function getFile(int|string $id, array $options = []): ?array {
		return $this->entityFileService->getFile($id, $options);
	}

	public function getFileContent(int|string $id, array $options = []): ?string {
		return $this->entityFileService->getFileContent($id, $options);
	}

	public function deleteFile(int|string $id, array $options = []): bool {
		return $this->entityFileService->deleteFile($id, $options) ?? false;
	}
}
