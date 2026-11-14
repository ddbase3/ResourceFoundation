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
use Base3\ResourceFoundation\Dto\FieldMetadata;
use Base3\ResourceFoundation\Dto\JoinMetadata;
use Base3\ResourceFoundation\Dto\TableMetadata;

final class TableMetadataTest extends TestCase {

	public function testDefaultsRepresentAnUnrestrictedTableDefinition(): void {
		$table = new TableMetadata('users');
		$this->assertSame('users', $table->name);
		$this->assertNull($table->label);
		$this->assertNull($table->description);
		$this->assertSame('', $table->domain);
		$this->assertSame('', $table->category);
		$this->assertSame([], $table->tags);
		$this->assertSame([], $table->fields);
		$this->assertSame([], $table->joins);
		$this->assertSame([], $table->defaultFilters);
		$this->assertFalse($table->sensitive);
		$this->assertSame([], $table->position);
	}

	public function testSensitiveTablePreservesJoinAndFieldInstances(): void {
		$field = new FieldMetadata('email', 'string', sensitive: true);
		$join = new JoinMetadata('groups', ['users.group_id' => 'groups.id']);
		$table = new TableMetadata('users', 'Users', 'Accounts', 'directory', 'personal', ['sensitive'], [$field], [$join], [['field' => 'active', 'value' => true]], true, ['weight' => 2]);
		$this->assertSame('Users', $table->label);
		$this->assertSame('Accounts', $table->description);
		$this->assertSame('directory', $table->domain);
		$this->assertSame('personal', $table->category);
		$this->assertSame([$field], $table->fields);
		$this->assertSame([$join], $table->joins);
		$this->assertSame([['field' => 'active', 'value' => true]], $table->defaultFilters);
		$this->assertTrue($table->sensitive);
		$this->assertSame(['weight' => 2], $table->position);
	}
}
