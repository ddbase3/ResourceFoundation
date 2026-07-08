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
 * Provides relation management for entities.
 *
 * A relation is a backend-defined link between two entity identifiers. Memora
 * implements this with undirected alloc rows, but other backends may implement
 * directed links, graph edges, or typed relations behind the same contract.
 */
interface IEntityRelationService {

	/**
	 * Returns relation rows for an entity.
	 *
	 * @param int|string $entryId Entity identifier
	 * @return array<int,array<string,mixed>> Relation rows
	 */
	public function getRelations(int|string $entryId): array;

	/**
	 * Returns identifiers of related peer entities.
	 *
	 * @param int|string $entryId Entity identifier
	 * @return array<int,int> Peer entity identifiers
	 */
	public function getRelationIds(int|string $entryId): array;

	/**
	 * Adds relations to peer entities.
	 *
	 * @param int|string $entryId Entity identifier
	 * @param array<int,int|string> $peerIds Peer entity identifiers
	 */
	public function addRelations(int|string $entryId, array $peerIds): void;

	/**
	 * Removes relations to peer entities.
	 *
	 * @param int|string $entryId Entity identifier
	 * @param array<int,int|string> $peerIds Peer entity identifiers
	 */
	public function removeRelations(int|string $entryId, array $peerIds): void;

	/**
	 * Replaces all relations of an entity.
	 *
	 * @param int|string $entryId Entity identifier
	 * @param array<int,int|string> $peerIds Peer entity identifiers
	 */
	public function replaceRelations(int|string $entryId, array $peerIds): void;
}
