<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Dto;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Dto\MaterializationGeneration;

final class MaterializationGenerationTest extends TestCase {

	public function testNewGenerationDefaultsToPublishedWithoutStatistics(): void {
		$generation = new MaterializationGeneration('analytics', 'overview', 'overview_0001', '0001');
		$this->assertSame('analytics', $generation->schema);
		$this->assertSame('overview', $generation->logicalTable);
		$this->assertSame('overview_0001', $generation->physicalTable);
		$this->assertSame('0001', $generation->generation);
		$this->assertSame('', $generation->schemaHash);
		$this->assertSame('', $generation->queryHash);
		$this->assertNull($generation->rowCount);
		$this->assertSame('published', $generation->status);
		$this->assertNull($generation->publishedAt);
		$this->assertSame([], $generation->meta);
	}

	public function testExplicitGenerationDetailsRetainFailureOrStagingState(): void {
		$generation = new MaterializationGeneration('analytics', 'overview', 'overview_0002', '0002', 'schema-hash', 'query-hash', 0, 'staging', 1762200000, ['backend' => 'mysql']);
		$this->assertSame('schema-hash', $generation->schemaHash);
		$this->assertSame('query-hash', $generation->queryHash);
		$this->assertSame(0, $generation->rowCount);
		$this->assertSame('staging', $generation->status);
		$this->assertSame(1762200000, $generation->publishedAt);
		$this->assertSame(['backend' => 'mysql'], $generation->meta);
	}
}
