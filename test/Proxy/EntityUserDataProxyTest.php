<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Proxy;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Proxy\EntityUserDataProxy;
use RuntimeException;

final class EntityUserDataProxyTest extends TestCase {

	public function testUserSpecificDataQueryRetainsTargetIdentity(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['getUserData'] = ['layout' => 'dense', 'favorite' => true];
		$proxy = new EntityUserDataProxy($connector);
		$this->assertSame(['layout' => 'dense', 'favorite' => true], $proxy->getUserData('entry-1', 12));
		$this->assertSame([['getUserData', ['entry-1', 12]]], $connector->calls);
	}

	public function testMissingUserDataIsEmptyAndKeepsNullCurrentUser(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityUserDataProxy($connector);
		$this->assertSame([], $proxy->getUserData(1));
		$this->assertSame([['getUserData', [1, null]]], $connector->calls);
	}

	public function testSingleValueForwardsDefaultAndUserArguments(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['getUserDataValue'] = false;
		$proxy = new EntityUserDataProxy($connector);
		$this->assertFalse($proxy->getUserDataValue(3, 'enabled', true, 17));
		$this->assertSame([['getUserDataValue', [3, 'enabled', true, 17]]], $connector->calls);
	}

	public function testMissingUserDataValueRemainsNull(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityUserDataProxy($connector);
		$this->assertNull($proxy->getUserDataValue(2, 'missing', 'default'));
		$this->assertSame([['getUserDataValue', [2, 'missing', 'default', null]]], $connector->calls);
	}

	public function testUserDataMutationsPreserveUserIdAndNames(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityUserDataProxy($connector);
		$proxy->setUserData(5, ['hidden' => true], 8);
		$proxy->removeUserData('entry-5', ['hidden', 'layout']);
		$this->assertSame([
			['setUserData', [5, ['hidden' => true], 8]],
			['removeUserData', ['entry-5', ['hidden', 'layout'], null]]
		], $connector->calls);
	}

	public function testBackendWriteExceptionDoesNotBecomeSuccess(): void {
		$connector = new RecordingEntityConnector();
		$connector->failures['setUserData'] = new RuntimeException('not authorized');
		$this->expectException(RuntimeException::class);
		(new EntityUserDataProxy($connector))->setUserData(1, ['private' => true], 2);
	}
}

require_once __DIR__ . '/RecordingEntityConnector.php';
