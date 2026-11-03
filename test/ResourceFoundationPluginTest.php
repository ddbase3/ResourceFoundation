<?php declare(strict_types=1);

namespace ResourceFoundation\Test;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\ResourceFoundationPlugin;
use Base3\Api\IContainer;

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
