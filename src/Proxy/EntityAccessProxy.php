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
use ResourceFoundation\Api\IEntityAccessService;

class EntityAccessProxy implements IEntityAccessService {

	public function __construct(
		private readonly IMicroserviceConnector $entityAccessService
	) {}


	public function getEntryAccess(int|string $entryId): array {
		return $this->entityAccessService->getEntryAccess($entryId) ?? [];
	}


	public function replaceEntryUserAccess(int|string $entryId, array $access): void {
		$this->entityAccessService->replaceEntryUserAccess($entryId, $access);
	}


	public function replaceEntryGroupAccess(int|string $entryId, array $access): void {
		$this->entityAccessService->replaceEntryGroupAccess($entryId, $access);
	}


	public function getRoles(bool $includeArchived = false): array {
		return $this->entityAccessService->getRoles($includeArchived) ?? [];
	}


	public function getRole(int|string $roleId): ?array {
		return $this->entityAccessService->getRole($roleId);
	}


	public function createRole(array $role): int|string {
		return $this->entityAccessService->createRole($role) ?? 0;
	}


	public function updateRole(int|string $roleId, array $patch): void {
		$this->entityAccessService->updateRole($roleId, $patch);
	}


	public function archiveRole(int|string $roleId): void {
		$this->entityAccessService->archiveRole($roleId);
	}


	public function getPermissions(bool $includeArchived = false): array {
		return $this->entityAccessService->getPermissions($includeArchived) ?? [];
	}


	public function getPermission(int|string $permissionId): ?array {
		return $this->entityAccessService->getPermission($permissionId);
	}


	public function createPermission(array $permission): int|string {
		return $this->entityAccessService->createPermission($permission) ?? 0;
	}


	public function updatePermission(int|string $permissionId, array $patch): void {
		$this->entityAccessService->updatePermission($permissionId, $patch);
	}


	public function archivePermission(int|string $permissionId): void {
		$this->entityAccessService->archivePermission($permissionId);
	}


	public function getRolePermissions(int|string $roleId): array {
		return $this->entityAccessService->getRolePermissions($roleId) ?? [];
	}


	public function replaceRolePermissions(int|string $roleId, array $permissionIds): void {
		$this->entityAccessService->replaceRolePermissions($roleId, $permissionIds);
	}


	public function getUserRoles(int|string $userId): array {
		return $this->entityAccessService->getUserRoles($userId) ?? [];
	}


	public function getGroupRoles(int|string $groupId): array {
		return $this->entityAccessService->getGroupRoles($groupId) ?? [];
	}


	public function getEffectiveUserRoles(int|string $userId): array {
		return $this->entityAccessService->getEffectiveUserRoles($userId) ?? [];
	}


	public function replaceUserRoles(int|string $userId, array $roleIds): void {
		$this->entityAccessService->replaceUserRoles($userId, $roleIds);
	}


	public function replaceGroupRoles(int|string $groupId, array $roleIds): void {
		$this->entityAccessService->replaceGroupRoles($groupId, $roleIds);
	}


	public function getUserGroups(int|string $userId): array {
		return $this->entityAccessService->getUserGroups($userId) ?? [];
	}


	public function replaceUserGroups(int|string $userId, array $groupIds): void {
		$this->entityAccessService->replaceUserGroups($userId, $groupIds);
	}
}
