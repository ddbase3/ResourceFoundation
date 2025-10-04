<?php declare(strict_types=1);

namespace ResourceApi;

use Base3\Api\IContainer;
use Base3\Api\IPlugin;

class ResourceApiPlugin implements IPlugin {

	public function __construct(private readonly IContainer $container) {}

	// Implementation of IBase

	public static function getName(): string {
		return 'resourceapiplugin';
	}

	// Implementation of IPlugin

	public function init() {
		$this->container
			->set(self::getName(), $this, IContainer::SHARED);
	}
}
