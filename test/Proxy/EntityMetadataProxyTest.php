<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Proxy;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Proxy\EntityMetadataProxy;
use RuntimeException;

final class EntityMetadataProxyTest extends TestCase {

	public function testMetadataListReturnsRemoteEntriesWithoutConversion(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['getMetadata'] = ['external_id' => 'abc', 'private' => false];
		$proxy = new EntityMetadataProxy($connector);
		$this->assertSame(['external_id' => 'abc', 'private' => false], $proxy->getMetadata('entry-1'));
		$this->assertSame([['getMetadata', ['entry-1']]], $connector->calls);
	}

	public function testNoMetadataResponseReturnsEmptyList(): void {
		$connector = new RecordingEntityConnector();
		$this->assertSame([], (new EntityMetadataProxy($connector))->getMetadata(1));
	}

	public function testGetMetadataValueForwardsCallerDefaultAndResult(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['getMetadataValue'] = 0;
		$proxy = new EntityMetadataProxy($connector);
		$this->assertSame(0, $proxy->getMetadataValue(2, 'count', 99));
		$this->assertSame([['getMetadataValue', [2, 'count', 99]]], $connector->calls);
	}

	public function testMissingMetadataValueReturnsNullInsteadOfFabricatingDefault(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityMetadataProxy($connector);
		$this->assertNull($proxy->getMetadataValue(2, 'unknown', 'fallback'));
		$this->assertSame([['getMetadataValue', [2, 'unknown', 'fallback']]], $connector->calls);
	}

	public function testSetRemoveAndReplaceMetadataUseDistinctConnectorCalls(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityMetadataProxy($connector);
		$proxy->setMetadata(3, ['status' => 'active']);
		$proxy->removeMetadata('entry-3', ['status', 'external_id']);
		$proxy->replaceMetadata(3, []);
		$this->assertSame([
			['setMetadata', [3, ['status' => 'active']]],
			['removeMetadata', ['entry-3', ['status', 'external_id']]],
			['replaceMetadata', [3, []]]
		], $connector->calls);
	}

	public function testMetadataWriteErrorIsNotSwallowed(): void {
		$connector = new RecordingEntityConnector();
		$connector->failures['replaceMetadata'] = new RuntimeException('access denied');
		$this->expectException(RuntimeException::class);
		(new EntityMetadataProxy($connector))->replaceMetadata(5, []);
	}
}

require_once __DIR__ . '/RecordingEntityConnector.php';
