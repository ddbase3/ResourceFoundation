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
use Base3\ResourceFoundation\Dto\TableNameResolutionContext;

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
