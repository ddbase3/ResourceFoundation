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

final class QueryStatementParametersTest extends TestCase {

	public function testStatementPreservesParametersSelectedFieldsAndSensitiveFlag(): void {
		$fields = [['field' => 'email', 'alias' => 'user_email', 'sensitive' => true]];
		$statement = new QueryStatement('SELECT email FROM users WHERE id = :id', ['id' => 7], $fields, true);
		$this->assertSame('SELECT email FROM users WHERE id = :id', $statement->sql);
		$this->assertSame(['id' => 7], $statement->params);
		$this->assertSame($fields, $statement->fields);
		$this->assertTrue($statement->sensitive);
		$this->assertFalse($statement->isWildcardQuery);
	}

	public function testWildcardQueriesRemainExplicitEvenWithNoSelectedFieldMetadata(): void {
		$statement = new QueryStatement('SELECT * FROM users', [], [], false, true);
		$this->assertTrue($statement->isWildcardQuery);
		$this->assertFalse($statement->sensitive);
		$this->assertSame([], $statement->fields);
	}
}
