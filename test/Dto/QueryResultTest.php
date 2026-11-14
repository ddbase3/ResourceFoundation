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
use Base3\ResourceFoundation\Dto\QueryResult;

final class QueryResultTest extends TestCase {

	public function testReadOnlyResultKeepsColumnsAndRowsWithNeutralDefaults(): void {
		$columns = [['name' => 'id', 'type' => 'int', 'sensitive' => false]];
		$rows = [['id' => 1], ['id' => 2]];
		$result = new QueryResult($columns, $rows);
		$this->assertSame($columns, $result->columns);
		$this->assertSame($rows, $result->rows);
		$this->assertNull($result->debugSql);
		$this->assertFalse($result->sensitive);
		$this->assertNull($result->affectedRows);
		$this->assertNull($result->insertId);
	}

	public function testSensitiveResultAndWriteStatisticsPreserveBackendIdentifiers(): void {
		$result = new QueryResult([], [], 'INSERT INTO secret ...', true, 1, 'uuid-123');
		$this->assertSame('INSERT INTO secret ...', $result->debugSql);
		$this->assertTrue($result->sensitive);
		$this->assertSame(1, $result->affectedRows);
		$this->assertSame('uuid-123', $result->insertId);
		$result->insertId = 123;
		$this->assertSame(123, $result->insertId);
	}
}
