<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Dto;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Dto\TableNameResolutionContext;

final class TableNameResolutionContextTest extends TestCase {

	public function testDefaultContextUsesLogicalModeWithoutSchema(): void {
		$context = new TableNameResolutionContext();
		$this->assertSame('', $context->schema);
		$this->assertNull($context->alias);
		$this->assertNull($context->operation);
		$this->assertSame('logical', $context->mode);
		$this->assertSame([], $context->options);
	}

	public function testPhysicalContextPreservesCallerOperationAndOptions(): void {
		$context = new TableNameResolutionContext('analytics', 'a', 'read', 'physical', ['generation' => 'current']);
		$this->assertSame('analytics', $context->schema);
		$this->assertSame('a', $context->alias);
		$this->assertSame('read', $context->operation);
		$this->assertSame('physical', $context->mode);
		$this->assertSame(['generation' => 'current'], $context->options);
	}
}
