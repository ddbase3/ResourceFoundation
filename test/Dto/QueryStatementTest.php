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
use Base3\ResourceFoundation\Dto\QueryStatement;

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
