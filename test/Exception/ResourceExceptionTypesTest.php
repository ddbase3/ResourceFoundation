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

namespace Base3\ResourceFoundation\Test\Exception;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Base3\ResourceFoundation\Exception\AccessDeniedException;
use Base3\ResourceFoundation\Exception\MaterializationException;
use Base3\ResourceFoundation\Exception\QueryValidationException;
use RuntimeException;

final class ResourceExceptionTypesTest extends TestCase {

	public function testPermissionErrorIsRuntimeExceptionWithoutLeakingAdditionalData(): void {
		$error = new AccessDeniedException('permission denied', 403);
		$this->assertInstanceOf(RuntimeException::class, $error);
		$this->assertSame('permission denied', $error->getMessage());
		$this->assertSame(403, $error->getCode());
	}

	public function testQueryValidationErrorUsesInvalidArgumentCategory(): void {
		$error = new QueryValidationException('invalid field');
		$this->assertInstanceOf(InvalidArgumentException::class, $error);
		$this->assertSame('invalid field', $error->getMessage());
	}

	public function testMaterializationErrorUsesRuntimeExceptionCategory(): void {
		$error = new MaterializationException('generation could not be published');
		$this->assertInstanceOf(RuntimeException::class, $error);
		$this->assertSame('generation could not be published', $error->getMessage());
	}
}
