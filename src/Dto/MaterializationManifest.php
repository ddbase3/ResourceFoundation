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

namespace ResourceFoundation\Dto;

class MaterializationManifest {

	public function __construct(
		public string $id,
		public string $sourceSchema = '',
		public string $targetSchema = '',
		public string $logicalTable = '',
		public string $physicalPrefix = '',
		public array $query = [],
		public array $columns = [],
		public array $indexes = [],
		public array $refresh = [],
		public array $options = [],
		public array $dependsOn = [],
		public bool $enabled = true,
		public int $priority = 100,
		public string $dependencyRefresh = 'missing',
		public array $schedule = []
	) {}

	public static function fromArray(array $data): self {
		$target = is_array($data['target'] ?? null) ? $data['target'] : [];
		$refresh = is_array($data['refresh'] ?? null) ? $data['refresh'] : [];
		$options = is_array($data['options'] ?? null) ? $data['options'] : [];
		$options = self::mergeTargetOptions($options, $target);

		return new self(
			id: (string)($data['id'] ?? ''),
			sourceSchema: (string)($data['sourceSchema'] ?? $data['source_schema'] ?? ''),
			targetSchema: (string)($data['targetSchema'] ?? $data['target_schema'] ?? ''),
			logicalTable: (string)($data['logicalTable'] ?? $data['logical_table'] ?? $target['logicalTable'] ?? $target['logical_table'] ?? ''),
			physicalPrefix: (string)($data['physicalPrefix'] ?? $data['physical_prefix'] ?? $target['physicalPrefix'] ?? $target['physical_prefix'] ?? ''),
			query: is_array($data['query'] ?? null) ? $data['query'] : [],
			columns: is_array($data['columns'] ?? null) ? $data['columns'] : [],
			indexes: is_array($data['indexes'] ?? null) ? $data['indexes'] : [],
			refresh: $refresh,
			options: $options,
			dependsOn: self::normalizeDependsOn($data['dependsOn'] ?? $data['depends_on'] ?? []),
			enabled: self::normalizeBool($data['enabled'] ?? $refresh['enabled'] ?? true),
			priority: self::normalizePriority($data['priority'] ?? $refresh['priority'] ?? 100),
			dependencyRefresh: self::normalizeDependencyRefresh(
				$data['dependencyRefresh'] ?? $data['dependency_refresh'] ?? $refresh['dependencyRefresh'] ?? $refresh['dependency_refresh'] ?? 'missing'
			),
			schedule: self::normalizeSchedule($data['schedule'] ?? $refresh['schedule'] ?? [])
		);
	}

	public function toArray(): array {
		return [
			'id' => $this->id,
			'sourceSchema' => $this->sourceSchema,
			'targetSchema' => $this->targetSchema,
			'logicalTable' => $this->logicalTable,
			'physicalPrefix' => $this->physicalPrefix,
			'query' => $this->query,
			'columns' => $this->columns,
			'indexes' => $this->indexes,
			'refresh' => $this->refresh,
			'options' => $this->options,
			'dependsOn' => $this->dependsOn,
			'enabled' => $this->enabled,
			'priority' => $this->priority,
			'dependencyRefresh' => $this->dependencyRefresh,
			'schedule' => $this->schedule
		];
	}

	public function getSchemaHash(): string {
		return hash('sha256', json_encode([
			'targetSchema' => $this->targetSchema,
			'logicalTable' => $this->logicalTable,
			'columns' => $this->columns,
			'indexes' => $this->indexes
		], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	}

	public function getQueryHash(): string {
		return hash('sha256', json_encode($this->query, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	}

	private static function mergeTargetOptions(array $options, array $target): array {
		foreach ([
			'publishStrategy',
			'publish_strategy',
			'keepGenerations',
			'keep_generations'
		] as $key) {
			if (array_key_exists($key, $target) && !array_key_exists($key, $options)) {
				$options[$key] = $target[$key];
			}
		}

		return $options;
	}

	/**
	 * @return string[]
	 */
	private static function normalizeDependsOn(mixed $value): array {
		if (is_string($value)) {
			$value = explode(',', $value);
		}

		if (!is_array($value)) {
			return [];
		}

		$result = [];
		foreach ($value as $dependency) {
			$dependency = trim((string)$dependency);
			if ($dependency !== '') {
				$result[] = $dependency;
			}
		}

		return array_values(array_unique($result));
	}

	private static function normalizeBool(mixed $value): bool {
		if (is_bool($value)) {
			return $value;
		}

		if (is_numeric($value)) {
			return (int)$value === 1;
		}

		$value = strtolower(trim((string)$value));
		return !in_array($value, ['0', 'false', 'no', 'off', 'disabled'], true);
	}

	private static function normalizePriority(mixed $value): int {
		$priority = (int)$value;
		return $priority > 0 ? $priority : 100;
	}

	private static function normalizeDependencyRefresh(mixed $value): string {
		$value = strtolower(trim((string)$value));
		return in_array($value, ['current', 'missing', 'due', 'cascade'], true) ? $value : 'missing';
	}

	private static function normalizeSchedule(mixed $value): array {
		if (!is_array($value)) {
			return [
				'policy' => 'interval',
				'seconds' => 300
			];
		}

		$policy = strtolower(trim((string)($value['policy'] ?? 'interval')));
		if (!in_array($policy, ['interval', 'daily_after', 'manual', 'always'], true)) {
			$policy = 'interval';
		}

		$value['policy'] = $policy;

		if ($policy === 'interval') {
			$seconds = (int)($value['seconds'] ?? $value['intervalSeconds'] ?? $value['interval_seconds'] ?? 300);
			$value['seconds'] = $seconds > 0 ? $seconds : 300;
		}

		if ($policy === 'daily_after') {
			$time = trim((string)($value['time'] ?? '02:00'));
			$value['time'] = preg_match('/^\d{2}:\d{2}$/', $time) === 1 ? $time : '02:00';
		}

		return $value;
	}
}
