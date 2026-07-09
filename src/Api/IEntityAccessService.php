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
 * Provides access-control administration for entity backends.
 *
 * Entry-level ACL grants stay attached directly to users and groups. Reusable
 * roles and permissions are administered separately through role, permission,
 * user-role, group-role, and role-permission relations.
 */
interface IEntityAccessService {

	/**
	 * Returns all direct access grants for an entry.
	 *
	 * The returned array should use the keys useraccess and groupaccess.
	 *
	 * @param int|string $entryId Entity identifier
	 * @return array<string,array<int,array<string,mixed>>> Access grants grouped by grant type
	 */
	public function getEntryAccess(int|string $entryId): array;

	/**
	 * Replaces the complete user-access list for an entry.
	 *
	 * @param int|string $entryId Entity identifier
	 * @param array<int,array<string,mixed>> $access User access rows
	 */
	public function replaceEntryUserAccess(int|string $entryId, array $access): void;

	/**
	 * Replaces the complete group-access list for an entry.
	 *
	 * @param int|string $entryId Entity identifier
	 * @param array<int,array<string,mixed>> $access Group access rows
	 */
	public function replaceEntryGroupAccess(int|string $entryId, array $access): void;

	/**
	 * Returns known roles.
	 *
	 * @param bool $includeArchived Whether archived roles should be returned
	 * @return array<int,array<string,mixed>> Role rows
	 */
	public function getRoles(bool $includeArchived = false): array;

	/**
	 * Returns one role by identifier.
	 *
	 * @param int|string $roleId Role identifier
	 * @return array<string,mixed>|null Role row or null
	 */
	public function getRole(int|string $roleId): ?array;

	/**
	 * Creates a role.
	 *
	 * Common role keys are name, label, info, archive, permission_ids, and permissions.
	 *
	 * @param array<string,mixed> $role Role data
	 * @return int|string Created role identifier
	 */
	public function createRole(array $role): int|string;

	/**
	 * Updates a role.
	 *
	 * @param int|string $roleId Role identifier
	 * @param array<string,mixed> $patch Partial role data
	 */
	public function updateRole(int|string $roleId, array $patch): void;

	/**
	 * Archives a role.
	 *
	 * @param int|string $roleId Role identifier
	 */
	public function archiveRole(int|string $roleId): void;

	/**
	 * Returns known permissions.
	 *
	 * @param bool $includeArchived Whether archived permissions should be returned
	 * @return array<int,array<string,mixed>> Permission rows
	 */
	public function getPermissions(bool $includeArchived = false): array;

	/**
	 * Returns one permission by identifier.
	 *
	 * @param int|string $permissionId Permission identifier
	 * @return array<string,mixed>|null Permission row or null
	 */
	public function getPermission(int|string $permissionId): ?array;

	/**
	 * Creates a permission.
	 *
	 * Common permission keys are scope, permission, label, info, and archive.
	 *
	 * @param array<string,mixed> $permission Permission data
	 * @return int|string Created permission identifier
	 */
	public function createPermission(array $permission): int|string;

	/**
	 * Updates a permission.
	 *
	 * @param int|string $permissionId Permission identifier
	 * @param array<string,mixed> $patch Partial permission data
	 */
	public function updatePermission(int|string $permissionId, array $patch): void;

	/**
	 * Archives a permission.
	 *
	 * @param int|string $permissionId Permission identifier
	 */
	public function archivePermission(int|string $permissionId): void;

	/**
	 * Returns permissions assigned to a role.
	 *
	 * @param int|string $roleId Role identifier
	 * @return array<int,array<string,mixed>> Permission rows
	 */
	public function getRolePermissions(int|string $roleId): array;

	/**
	 * Replaces all permissions assigned to a role.
	 *
	 * @param int|string $roleId Role identifier
	 * @param array<int,int|string> $permissionIds Permission identifiers
	 */
	public function replaceRolePermissions(int|string $roleId, array $permissionIds): void;

	/**
	 * Returns roles directly assigned to a user.
	 *
	 * @param int|string $userId User identifier
	 * @return array<int,array<string,mixed>> Role rows
	 */
	public function getUserRoles(int|string $userId): array;

	/**
	 * Returns roles directly assigned to a group.
	 *
	 * @param int|string $groupId Group identifier
	 * @return array<int,array<string,mixed>> Role rows
	 */
	public function getGroupRoles(int|string $groupId): array;

	/**
	 * Returns effective roles for a user, combining direct user roles and roles
	 * inherited through the user's groups.
	 *
	 * @param int|string $userId User identifier
	 * @return array<int,array<string,mixed>> Role rows
	 */
	public function getEffectiveUserRoles(int|string $userId): array;

	/**
	 * Replaces all roles directly assigned to a user.
	 *
	 * @param int|string $userId User identifier
	 * @param array<int,int|string> $roleIds Role identifiers
	 */
	public function replaceUserRoles(int|string $userId, array $roleIds): void;

	/**
	 * Replaces all roles directly assigned to a group.
	 *
	 * @param int|string $groupId Group identifier
	 * @param array<int,int|string> $roleIds Role identifiers
	 */
	public function replaceGroupRoles(int|string $groupId, array $roleIds): void;

	/**
	 * Returns group identifiers for a user.
	 *
	 * @param int|string $userId User identifier
	 * @return array<int,int> Group identifiers
	 */
	public function getUserGroups(int|string $userId): array;

	/**
	 * Replaces all group memberships for a user.
	 *
	 * @param int|string $userId User identifier
	 * @param array<int,int|string> $groupIds Group identifiers
	 */
	public function replaceUserGroups(int|string $userId, array $groupIds): void;
}
