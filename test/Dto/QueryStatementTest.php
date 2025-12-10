<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Dto;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Dto\QueryStatement;

class QueryStatementTest extends TestCase {

	public function testConstructorAssignsDefaults(): void {
		$q = new QueryStatement('SELECT 1');

		$this->assertSame('SELECT 1', $q->sql);
		$this->assertSame([], $q->params);
		$this->assertSame([], $q->fields);
		$this->assertFalse($q->sensitive);
		$this->assertFalse($q->isWildcardQuery);
	}

}
