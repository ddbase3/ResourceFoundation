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
		public array $dependsOn = []
	) {}

	public static function fromArray(array $data): self {
		$target = is_array($data['target'] ?? null) ? $data['target'] : [];
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
			refresh: is_array($data['refresh'] ?? null) ? $data['refresh'] : [],
			options: $options,
			dependsOn: self::normalizeDependsOn($data['dependsOn'] ?? $data['depends_on'] ?? [])
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
			'dependsOn' => $this->dependsOn
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
}
