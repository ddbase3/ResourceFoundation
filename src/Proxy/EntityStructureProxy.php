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
use ResourceFoundation\Api\IEntityStructureService;

class EntityStructureProxy implements IEntityStructureService {

	public function __construct(
		private readonly IMicroserviceConnector $entityStructureService
	) {}


	public function getTypes(): array {
		return $this->entityStructureService->getTypes() ?? [];
	}


	public function getType(int|string $type): ?array {
		return $this->entityStructureService->getType($type);
	}


	public function createType(array $type): int|string {
		return $this->entityStructureService->createType($type) ?? 0;
	}


	public function updateType(int|string $typeId, array $patch): void {
		$this->entityStructureService->updateType($typeId, $patch);
	}


	public function getModules(): array {
		return $this->entityStructureService->getModules() ?? [];
	}


	public function getModule(string $module): ?array {
		return $this->entityStructureService->getModule($module);
	}


	public function createModule(array $module): string {
		return $this->entityStructureService->createModule($module) ?? "";
	}


	public function updateModule(string $module, array $patch): void {
		$this->entityStructureService->updateModule($module, $patch);
	}


	public function getScopes(): array {
		return $this->entityStructureService->getScopes() ?? [];
	}


	public function getScope(string $scope): ?array {
		return $this->entityStructureService->getScope($scope);
	}


	public function createScope(array $scope): string {
		return $this->entityStructureService->createScope($scope) ?? "";
	}


	public function getScopeModules(?string $scope = null): array {
		return $this->entityStructureService->getScopeModules($scope) ?? [];
	}


	public function getModuleScopes(?string $module = null): array {
		return $this->entityStructureService->getModuleScopes($module) ?? [];
	}


	public function assignModuleToScope(string $module, string $scope): void {
		$this->entityStructureService->assignModuleToScope($module, $scope);
	}


	public function removeModuleFromScope(string $module, string $scope): void {
		$this->entityStructureService->removeModuleFromScope($module, $scope);
	}
}
