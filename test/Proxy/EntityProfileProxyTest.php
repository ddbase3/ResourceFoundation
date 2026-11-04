<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Proxy;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Proxy\EntityProfileProxy;
use RuntimeException;

final class EntityProfileProxyTest extends TestCase {

	public function testActiveProfileAndUserIdArePassedThrough(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['getActiveProfile'] = ['id' => 8, 'name' => 'Dashboard'];
		$proxy = new EntityProfileProxy($connector);
		$this->assertSame(['id' => 8, 'name' => 'Dashboard'], $proxy->getActiveProfile(5));
		$this->assertSame([['getActiveProfile', [5]]], $connector->calls);
	}

	public function testOptionalUserAndArchivedFlagStayInArgumentOrder(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['getProfiles'] = [['id' => 1], ['id' => 2]];
		$proxy = new EntityProfileProxy($connector);
		$this->assertSame([['id' => 1], ['id' => 2]], $proxy->getProfiles(null, true));
		$this->assertSame([['getProfiles', [null, true]]], $connector->calls);
	}

	public function testMissingProfilesReturnEmptyListAndActiveProfileRemainsNull(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityProfileProxy($connector);
		$this->assertSame([], $proxy->getProfiles());
		$this->assertNull($proxy->getActiveProfile());
		$this->assertSame([['getProfiles', [null, false]], ['getActiveProfile', [null]]], $connector->calls);
	}

	public function testCreateProfileRetainsProvidedIdentityOrZeroIfUnavailable(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityProfileProxy($connector);
		$this->assertSame(0, $proxy->createProfile(7, ['name' => 'Mine']));
		$connector->responses['createProfile'] = 'new-profile';
		$this->assertSame('new-profile', $proxy->createProfile(8, ['name' => 'Team']));
		$this->assertSame([
			['createProfile', [7, ['name' => 'Mine']]],
			['createProfile', [8, ['name' => 'Team']]]
		], $connector->calls);
	}

	public function testUpdateArchiveAndActivationAreForwarded(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityProfileProxy($connector);
		$proxy->updateProfile('p2', ['name' => 'Renamed']);
		$proxy->archiveProfile('p1');
		$proxy->setActiveProfile(7, 2);
		$this->assertSame([
			['updateProfile', ['p2', ['name' => 'Renamed']]],
			['archiveProfile', ['p1']],
			['setActiveProfile', [7, 2]]
		], $connector->calls);
	}

	public function testCreateErrorsPropagateWithoutAlternatePaths(): void {
		$connector = new RecordingEntityConnector();
		$connector->failures['createProfile'] = new RuntimeException('denied');
		$this->expectException(RuntimeException::class);
		(new EntityProfileProxy($connector))->createProfile(1, []);
	}
}

require_once __DIR__ . '/RecordingEntityConnector.php';
