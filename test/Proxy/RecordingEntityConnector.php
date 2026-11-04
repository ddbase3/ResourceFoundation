<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Proxy;

use Base3\Microservice\Api\IMicroserviceConnector;
use Throwable;

final class RecordingEntityConnector implements IMicroserviceConnector {

	public array $calls = [];
	public array $responses = [];
	public array $failures = [];

	public function getMicroserviceUrl(): ?string {
		return 'test://resource-connector';
	}

	public function __call(string $method, array $arguments): mixed {
		$this->calls[] = [$method, $arguments];
		if (isset($this->failures[$method]) && $this->failures[$method] instanceof Throwable) {
			throw $this->failures[$method];
		}
		return $this->responses[$method] ?? null;
	}
}
