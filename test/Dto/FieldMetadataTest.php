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
use Base3\ResourceFoundation\Dto\ForeignKeyReference;

final class FieldMetadataTest extends TestCase {

	public function testDefaultsDescribeOrdinaryPublicColumn(): void {
		$field = new FieldMetadata('title', 'string');
		$this->assertSame('title', $field->name);
		$this->assertSame('string', $field->type);
		$this->assertNull($field->description);
		$this->assertFalse($field->primaryKey);
		$this->assertNull($field->foreignKey);
		$this->assertFalse($field->nullable);
		$this->assertSame([], $field->tags);
		$this->assertNull($field->alias);
		$this->assertFalse($field->sensitive);
	}

	public function testSensitiveForeignKeyAndMetadataAreRetained(): void {
		$reference = new ForeignKeyReference('users', 'id');
		$field = new FieldMetadata('owner', 'integer', 'Owner', true, $reference, true, ['personal', 'internal'], 'user_id', true);
		$this->assertSame('Owner', $field->description);
		$this->assertTrue($field->primaryKey);
		$this->assertSame($reference, $field->foreignKey);
		$this->assertTrue($field->nullable);
		$this->assertSame(['personal', 'internal'], $field->tags);
		$this->assertSame('user_id', $field->alias);
		$this->assertTrue($field->sensitive);
	}
}
