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

namespace Base3\ResourceFoundation\Test\Proxy;

use PHPUnit\Framework\TestCase;
use Base3\ResourceFoundation\Proxy\EntityRelationProxy;
use RuntimeException;

final class EntityRelationProxyTest extends TestCase {

	public function testRelationQueriesReturnRemoteDataAndPreserveIds(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['getRelations'] = [['peer' => 'b', 'type' => 'friend']];
		$connector->responses['getRelationIds'] = ['b', 'c'];
		$proxy = new EntityRelationProxy($connector);
		$this->assertSame([['peer' => 'b', 'type' => 'friend']], $proxy->getRelations('a'));
		$this->assertSame(['b', 'c'], $proxy->getRelationIds(1));
		$this->assertSame([['getRelations', ['a']], ['getRelationIds', [1]]], $connector->calls);
	}

	public function testMissingRelationsAreEmptyWithoutTreatingThemAsErrors(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityRelationProxy($connector);
		$this->assertSame([], $proxy->getRelations('missing'));
		$this->assertSame([], $proxy->getRelationIds('missing'));
	}

	public function testRelationMutationsRemainSeparateOperations(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityRelationProxy($connector);
		$proxy->addRelations('entry', [2, 'peer-3']);
		$proxy->removeRelations('entry', [2]);
		$proxy->replaceRelations('entry', []);
		$this->assertSame([
			['addRelations', ['entry', [2, 'peer-3']]],
			['removeRelations', ['entry', [2]]],
			['replaceRelations', ['entry', []]]
		], $connector->calls);
	}

	public function testMutationFailureIsPassedThrough(): void {
		$connector = new RecordingEntityConnector();
		$connector->failures['removeRelations'] = new RuntimeException('unavailable');
		$this->expectException(RuntimeException::class);
		(new EntityRelationProxy($connector))->removeRelations(1, [2]);
	}
}

require_once __DIR__ . '/RecordingEntityConnector.php';
