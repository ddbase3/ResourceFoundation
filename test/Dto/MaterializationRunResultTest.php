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
use Base3\ResourceFoundation\Dto\MaterializationGeneration;
use Base3\ResourceFoundation\Dto\MaterializationRunResult;

final class MaterializationRunResultTest extends TestCase {

	public function testFailedRunHasNoPublishedGenerationByDefault(): void {
		$result = new MaterializationRunResult('report', false);
		$this->assertSame('report', $result->manifestId);
		$this->assertFalse($result->success);
		$this->assertSame('', $result->message);
		$this->assertNull($result->generation);
		$this->assertNull($result->rowCount);
		$this->assertSame([], $result->meta);
	}

	public function testSuccessfulRunCarriesSameGenerationObjectAndZeroRowCount(): void {
		$generation = new MaterializationGeneration('analytics', 'daily', 'daily_v2', 'v2', rowCount: 0);
		$result = new MaterializationRunResult('daily', true, 'done', $generation, 0, ['duration_ms' => 12]);
		$this->assertTrue($result->success);
		$this->assertSame('done', $result->message);
		$this->assertSame($generation, $result->generation);
		$this->assertSame(0, $result->rowCount);
		$this->assertSame(['duration_ms' => 12], $result->meta);
	}
}
