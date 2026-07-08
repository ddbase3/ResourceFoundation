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
 * Provides tag and tag-catalog management for entities.
 */
interface IEntityTagService {

	/**
	 * Returns tags assigned to an entity.
	 *
	 * @param int|string $entryId Entity identifier
	 * @return array<int,string> Tag names
	 */
	public function getEntryTags(int|string $entryId): array;

	/**
	 * Adds tags to an entity.
	 *
	 * @param int|string $entryId Entity identifier
	 * @param array<int,string> $tags Tags to add
	 */
	public function addEntryTags(int|string $entryId, array $tags): void;

	/**
	 * Removes tags from an entity.
	 *
	 * @param int|string $entryId Entity identifier
	 * @param array<int,string> $tags Tags to remove
	 */
	public function removeEntryTags(int|string $entryId, array $tags): void;

	/**
	 * Replaces all tags of an entity.
	 *
	 * @param int|string $entryId Entity identifier
	 * @param array<int,string> $tags New tag list
	 */
	public function replaceEntryTags(int|string $entryId, array $tags): void;

	/**
	 * Returns known tag descriptions.
	 *
	 * @param string|null $scope Optional scope filter
	 * @return array<int,array<string,mixed>> Tag rows
	 */
	public function getTags(?string $scope = null): array;

	/**
	 * Returns module-tag assignments.
	 *
	 * When $module is null, all assignments are returned. When $module is set,
	 * only assignments for that module are returned.
	 *
	 * @return array<int,array{module:string,tag:string}>
	 */
	public function getModuleTags(?string $module = null): array;

	/**
	 * Creates or updates a tag description.
	 *
	 * @param string $tag Tag name
	 * @param string $description Description text
	 */
	public function describeTag(string $tag, string $description): void;

	/** Assigns a tag to a scope. */
	public function assignTagToScope(string $tag, string $scope): void;

	/** Removes a tag from a scope. */
	public function removeTagFromScope(string $tag, string $scope): void;

	/** Assigns a tag to a module. */
	public function assignTagToModule(string $tag, string $module): void;

	/** Removes a tag from a module. */
	public function removeTagFromModule(string $tag, string $module): void;
}
