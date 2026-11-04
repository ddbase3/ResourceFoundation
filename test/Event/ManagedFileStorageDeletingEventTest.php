<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Event;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Event\ManagedFileStorageDeletingEvent;

final class ManagedFileStorageDeletingEventTest extends TestCase {

	public function testEventRetainsOwningStorageIdentityAndDeletionMode(): void {
		$event = new ManagedFileStorageDeletingEvent('media', 'course-123', 'recursive');
		$this->assertSame('media', $event->getOwnerGroup());
		$this->assertSame('course-123', $event->getOwnerName());
		$this->assertSame('recursive', $event->getMode());
	}

	public function testBlankOrOpaqueOwnerNamesAreNotRewritten(): void {
		$event = new ManagedFileStorageDeletingEvent('', 'storage/uuid', 'detach');
		$this->assertSame('', $event->getOwnerGroup());
		$this->assertSame('storage/uuid', $event->getOwnerName());
		$this->assertSame('detach', $event->getMode());
	}
}
