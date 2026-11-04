<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Dto;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Dto\JoinMetadata;

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
