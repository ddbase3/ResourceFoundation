<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Dto;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Dto\ForeignKeyReference;

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
