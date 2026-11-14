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

namespace Base3\ResourceFoundation\Test;

use PHPUnit\Framework\TestCase;
use Base3\ResourceFoundation\ResourceFoundationPlugin;
use Base3\CoreFoundation\Api\IContainer;

class ResourceFoundationPluginTest extends TestCase {

	public function testGetNameReturnsExpectedValue(): void {
		$this->assertSame('resourcefoundationplugin', ResourceFoundationPlugin::getName());
	}

	public function testInitRegistersPluginInContainerAsShared(): void {
		$container = new FakeContainer();
		$plugin = new ResourceFoundationPlugin($container);

		$plugin->init();

		$this->assertTrue($container->has(ResourceFoundationPlugin::getName()));
		$this->assertSame(IContainer::SHARED, $container->getFlags(ResourceFoundationPlugin::getName()));
		$this->assertSame($plugin, $container->get(ResourceFoundationPlugin::getName()));
	}

}

require_once __DIR__ . '/FakeContainer.php';
