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

namespace Base3\ResourceFoundation\Test\Dto;

use PHPUnit\Framework\TestCase;
use Base3\ResourceFoundation\Dto\JoinMetadata;

final class JoinMetadataTest extends TestCase {

	public function testDefaultJoinIsInnerWithoutOptions(): void {
		$join = new JoinMetadata('users', ['orders.user_id' => 'users.id']);
		$this->assertSame('users', $join->targetTable);
		$this->assertSame(['orders.user_id' => 'users.id'], $join->on);
		$this->assertSame('INNER', $join->type);
		$this->assertSame([], $join->meta);
	}

	public function testExplicitJoinTypeAndDefaultMetadataAreRetained(): void {
		$join = new JoinMetadata('groups', [['left' => 'users.group_id', 'right' => 'groups.id']], 'LEFT', ['default' => true, 'weight' => 5]);
		$this->assertSame('LEFT', $join->type);
		$this->assertTrue($join->meta['default']);
		$this->assertSame(5, $join->meta['weight']);
		$this->assertSame('groups', $join->targetTable);
	}
}
