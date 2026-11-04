<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Exception;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ResourceFoundation\Exception\AccessDeniedException;
use ResourceFoundation\Exception\MaterializationException;
use ResourceFoundation\Exception\QueryValidationException;
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
