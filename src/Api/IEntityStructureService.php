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
 * Provides access to backend structure definitions.
 *
 * Structure services manage tables or records that define entity semantics such
 * as types, modules, scopes, and scope-module assignments. They are not meant
 * for normal business-entry CRUD, which belongs to IEntityDataService.
 */
interface IEntityStructureService {

	/** @return array<int,array<string,mixed>> Type rows */
	public function getTypes(): array;

	/** @return array<string,mixed>|null Type row */
	public function getType(int|string $type): ?array;

	/** @param array<string,mixed> $type Type data */
	public function createType(array $type): int|string;

	/** @param array<string,mixed> $patch Partial type data */
	public function updateType(int|string $typeId, array $patch): void;

	/** @return array<int,array<string,mixed>> Module rows */
	public function getModules(): array;

	/** @return array<string,mixed>|null Module row */
	public function getModule(string $module): ?array;

	/** @param array<string,mixed> $module Module data */
	public function createModule(array $module): string;

	/** @param array<string,mixed> $patch Partial module data */
	public function updateModule(string $module, array $patch): void;

	/** @return array<int,array<string,mixed>> Scope rows */
	public function getScopes(): array;

	/** @return array<string,mixed>|null Scope row */
	public function getScope(string $scope): ?array;

	/** @param array<string,mixed> $scope Scope data */
	public function createScope(array $scope): string;

	/**
	 * Returns scope-module assignments.
	 *
	 * When $scope is null, all assignments are returned. When $scope is set,
	 * only assignments for that scope are returned.
	 *
	 * @return array<int,array{scope:string,module:string}>
	 */
	public function getScopeModules(?string $scope = null): array;

	/**
	 * Returns module-scope assignments.
	 *
	 * When $module is null, all assignments are returned. When $module is set,
	 * only assignments for that module are returned.
	 *
	 * @return array<int,array{module:string,scope:string}>
	 */
	public function getModuleScopes(?string $module = null): array;

	/** Assigns a module to a scope. */
	public function assignModuleToScope(string $module, string $scope): void;

	/** Removes a module from a scope. */
	public function removeModuleFromScope(string $module, string $scope): void;
}
