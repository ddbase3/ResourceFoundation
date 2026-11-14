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

namespace Base3\ResourceFoundation\Test\Event;

use PHPUnit\Framework\TestCase;
use Base3\ResourceFoundation\Event\ManagedFileStorageDeletingEvent;

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
