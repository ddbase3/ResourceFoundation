<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Dto;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Dto\MaterializationGeneration;
use ResourceFoundation\Dto\MaterializationRunResult;

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
