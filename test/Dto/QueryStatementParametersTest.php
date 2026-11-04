<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Dto;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Dto\QueryStatement;

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
