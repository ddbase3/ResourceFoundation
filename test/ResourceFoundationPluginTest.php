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

class FakeContainer implements IContainer {

	private array $items = [];
	private array $flags = [];

	public function getServiceList(): array {
		return array_keys($this->items);
	}

	public function set(string $name, $classDefinition, $flags = 0): IContainer {
		$this->items[$name] = $classDefinition;
		$this->flags[$name] = (int)$flags;
		return $this;
	}

	public function remove(string $name) {
		unset($this->items[$name], $this->flags[$name]);
	}

	public function has(string $name): bool {
		return array_key_exists($name, $this->items);
	}

	public function get(string $name) {
		return $this->items[$name] ?? null;
	}

	public function getFlags(string $name): ?int {
		return $this->flags[$name] ?? null;
	}

}
