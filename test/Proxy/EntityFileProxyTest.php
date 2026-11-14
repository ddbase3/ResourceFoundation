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

namespace Base3\ResourceFoundation\Test\Proxy;

use PHPUnit\Framework\TestCase;
use Base3\ResourceFoundation\Proxy\EntityFileProxy;
use RuntimeException;

final class EntityFileProxyTest extends TestCase {

	public function testCreateAndReplaceForwardPayloadAndOptions(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['createFile'] = ['id' => 'file-1'];
		$connector->responses['replaceFile'] = ['id' => 'file-1', 'version' => 2];
		$proxy = new EntityFileProxy($connector);
		$this->assertSame(['id' => 'file-1'], $proxy->createFile(['name' => 'report.pdf'], ['scope' => 'private']));
		$this->assertSame(['id' => 'file-1', 'version' => 2], $proxy->replaceFile('file-1', ['name' => 'new.pdf']));
		$this->assertSame([
			['createFile', [['name' => 'report.pdf'], ['scope' => 'private']]],
			['replaceFile', ['file-1', ['name' => 'new.pdf'], []]]
		], $connector->calls);
	}

	public function testMissingWriteResultsFallBackToEmptyArrays(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityFileProxy($connector);
		$this->assertSame([], $proxy->createFile([]));
		$this->assertSame([], $proxy->replaceFile(7, [], ['ifMatch' => 'etag']));
	}

	public function testGetFileAndRawContentPreserveNullAndBinaryContent(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['getFile'] = ['id' => 7, 'mediaType' => 'application/octet-stream'];
		$connector->responses['getFileContent'] = "\x00\x01\xFF";
		$proxy = new EntityFileProxy($connector);
		$this->assertSame(['id' => 7, 'mediaType' => 'application/octet-stream'], $proxy->getFile(7, ['includeMeta' => true]));
		$this->assertSame("\x00\x01\xFF", $proxy->getFileContent('file-7', ['stream' => false]));
		$this->assertSame([
			['getFile', [7, ['includeMeta' => true]]],
			['getFileContent', ['file-7', ['stream' => false]]]
		], $connector->calls);
	}

	public function testUnknownFileReturnsNullForNullableReads(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityFileProxy($connector);
		$this->assertNull($proxy->getFile(10));
		$this->assertNull($proxy->getFileContent(10));
	}

	public function testDeletePreservesRemoteBooleanOrFalseForNoResponse(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityFileProxy($connector);
		$this->assertFalse($proxy->deleteFile(10));
		$connector->responses['deleteFile'] = true;
		$this->assertTrue($proxy->deleteFile('file-10', ['hard' => true]));
		$connector->responses['deleteFile'] = false;
		$this->assertFalse($proxy->deleteFile('file-11'));
		$this->assertSame([
			['deleteFile', [10, []]],
			['deleteFile', ['file-10', ['hard' => true]]],
			['deleteFile', ['file-11', []]]
		], $connector->calls);
	}

	public function testDeletionExceptionIsPassedThrough(): void {
		$connector = new RecordingEntityConnector();
		$connector->failures['deleteFile'] = new RuntimeException('storage unavailable');
		$this->expectException(RuntimeException::class);
		(new EntityFileProxy($connector))->deleteFile('file-1');
	}
}

require_once __DIR__ . '/RecordingEntityConnector.php';
