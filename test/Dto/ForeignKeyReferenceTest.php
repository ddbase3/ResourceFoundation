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
use Base3\ResourceFoundation\Dto\ForeignKeyReference;

final class ForeignKeyReferenceTest extends TestCase {

	public function testNamesArePreservedWithoutBackendSpecificConversion(): void {
		$reference = new ForeignKeyReference('public.user_accounts', 'external_id');
		$this->assertSame('public.user_accounts', $reference->table);
		$this->assertSame('external_id', $reference->column);
	}

	public function testIdentifiersRemainWritableForSchemaBuilders(): void {
		$reference = new ForeignKeyReference('draft', 'draft_id');
		$reference->table = 'published';
		$reference->column = 'id';
		$this->assertSame('published', $reference->table);
		$this->assertSame('id', $reference->column);
	}
}
