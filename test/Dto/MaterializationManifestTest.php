<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Dto;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Dto\MaterializationManifest;

final class MaterializationManifestTest extends TestCase {

	public function testConstructorDefaultsArePreservedByToArray(): void {
		$manifest = new MaterializationManifest('courses');
		$this->assertSame('courses', $manifest->id);
		$this->assertSame('', $manifest->sourceSchema);
		$this->assertSame('', $manifest->targetSchema);
		$this->assertSame('', $manifest->logicalTable);
		$this->assertSame([], $manifest->query);
		$this->assertSame([], $manifest->columns);
		$this->assertSame([], $manifest->indexes);
		$this->assertSame([], $manifest->dependsOn);
		$this->assertTrue($manifest->enabled);
		$this->assertSame(100, $manifest->priority);
		$this->assertSame('missing', $manifest->dependencyRefresh);
		$this->assertSame([], $manifest->schedule);
		$this->assertSame('courses', $manifest->toArray()['id']);
	}

	public function testFromArrayAcceptsSnakeCaseAndNestedTarget(): void {
		$manifest = MaterializationManifest::fromArray([
			'id' => 'course_counts',
			'source_schema' => 'source',
			'target_schema' => 'analytics',
			'target' => [
				'logical_table' => 'course_counts',
				'physical_prefix' => 'mat_',
				'publish_strategy' => 'swap',
				'keep_generations' => 3
			],
			'query' => ['from' => 'courses'],
			'columns' => [['name' => 'total', 'type' => 'int']],
			'indexes' => [['columns' => ['total']]]
		]);
		$this->assertSame('course_counts', $manifest->id);
		$this->assertSame('source', $manifest->sourceSchema);
		$this->assertSame('analytics', $manifest->targetSchema);
		$this->assertSame('course_counts', $manifest->logicalTable);
		$this->assertSame('mat_', $manifest->physicalPrefix);
		$this->assertSame(['publish_strategy' => 'swap', 'keep_generations' => 3], $manifest->options);
		$this->assertSame(['from' => 'courses'], $manifest->query);
		$this->assertSame([['name' => 'total', 'type' => 'int']], $manifest->columns);
		$this->assertSame([['columns' => ['total']]], $manifest->indexes);
		$this->assertSame(['policy' => 'interval', 'seconds' => 300], $manifest->schedule);
	}

	public function testExplicitOptionsOverrideTargetOptionsWithoutDiscardingOtherValues(): void {
		$manifest = MaterializationManifest::fromArray([
			'id' => 'report',
			'options' => ['publishStrategy' => 'replace', 'keepGenerations' => 7, 'audit' => true],
			'target' => ['publishStrategy' => 'swap', 'keepGenerations' => 2, 'keep_generations' => 5]
		]);
		$this->assertSame([
			'publishStrategy' => 'replace',
			'keepGenerations' => 7,
			'audit' => true,
			'keep_generations' => 5
		], $manifest->options);
	}

	public function testCamelCaseValuesTakePrecedenceOverSnakeCaseAndTarget(): void {
		$manifest = MaterializationManifest::fromArray([
			'id' => 'priority',
			'sourceSchema' => 'primary', 'source_schema' => 'secondary',
			'targetSchema' => 'one', 'target_schema' => 'two',
			'logicalTable' => 'explicit', 'logical_table' => 'legacy',
			'physicalPrefix' => 'explicit_', 'physical_prefix' => 'legacy_',
			'target' => ['logicalTable' => 'nested', 'physicalPrefix' => 'nested_']
		]);
		$this->assertSame('primary', $manifest->sourceSchema);
		$this->assertSame('one', $manifest->targetSchema);
		$this->assertSame('explicit', $manifest->logicalTable);
		$this->assertSame('explicit_', $manifest->physicalPrefix);
	}

	public function testNonArraySectionsBecomeEmptyArrays(): void {
		$manifest = MaterializationManifest::fromArray([
			'id' => 'invalid-sections',
			'target' => 'invalid', 'query' => 'invalid', 'columns' => false,
			'indexes' => 5, 'refresh' => true, 'options' => 'invalid',
			'dependsOn' => 42
		]);
		$this->assertSame([], $manifest->query);
		$this->assertSame([], $manifest->columns);
		$this->assertSame([], $manifest->indexes);
		$this->assertSame([], $manifest->refresh);
		$this->assertSame([], $manifest->options);
		$this->assertSame([], $manifest->dependsOn);
	}

	public function testDependsOnNormalizesCommaSeparatedValuesAndRemovesDuplicates(): void {
		$manifest = MaterializationManifest::fromArray([
			'id' => 'report', 'depends_on' => '  users, courses,, users,  metrics  '
		]);
		$this->assertSame(['users', 'courses', 'metrics'], $manifest->dependsOn);
		$manifest = MaterializationManifest::fromArray([
			'id' => 'report', 'dependsOn' => [' users ', '', 'courses', 'users', 5]
		]);
		$this->assertSame(['users', 'courses', '5'], $manifest->dependsOn);
	}

	public function testEnabledNormalizationRejectsExplicitFalseForms(): void {
		foreach ([false, 0, '0', 'FALSE', ' no ', 'OFF', 'Disabled', 2] as $value) {
			$this->assertFalse(MaterializationManifest::fromArray(['enabled' => $value])->enabled);
		}
		foreach ([true, 1, '1', 'true', 'yes', 'on', 'unexpected'] as $value) {
			$this->assertTrue(MaterializationManifest::fromArray(['enabled' => $value])->enabled);
		}
	}

	public function testRefreshDefaultsAndDirectOptionsTakePrecedence(): void {
		$manifest = MaterializationManifest::fromArray([
			'id' => 'daily',
			'refresh' => ['enabled' => false, 'priority' => 14, 'dependency_refresh' => 'cascade', 'schedule' => ['policy' => 'manual']]
		]);
		$this->assertFalse($manifest->enabled);
		$this->assertSame(14, $manifest->priority);
		$this->assertSame('cascade', $manifest->dependencyRefresh);
		$this->assertSame(['policy' => 'manual'], $manifest->schedule);
		$explicit = MaterializationManifest::fromArray([
			'enabled' => true, 'priority' => 3, 'dependencyRefresh' => 'due', 'schedule' => ['policy' => 'always'],
			'refresh' => ['enabled' => false, 'priority' => 15, 'dependencyRefresh' => 'current', 'schedule' => ['policy' => 'manual']]
		]);
		$this->assertTrue($explicit->enabled);
		$this->assertSame(3, $explicit->priority);
		$this->assertSame('due', $explicit->dependencyRefresh);
		$this->assertSame(['policy' => 'always'], $explicit->schedule);
	}

	public function testPriorityAndDependencyModeNormalizeInvalidInputs(): void {
		foreach ([-2, 0, 'invalid'] as $priority) {
			$this->assertSame(100, MaterializationManifest::fromArray(['priority' => $priority])->priority);
		}
		$this->assertSame(17, MaterializationManifest::fromArray(['priority' => '17'])->priority);
		foreach (['current', 'missing', 'due', 'cascade'] as $value) {
			$this->assertSame($value, MaterializationManifest::fromArray(['dependencyRefresh' => strtoupper($value)])->dependencyRefresh);
		}
		$this->assertSame('missing', MaterializationManifest::fromArray(['dependencyRefresh' => 'unknown'])->dependencyRefresh);
	}

	public function testIntervalScheduleNormalizesAliasesAndBadDurations(): void {
		$this->assertSame(['policy' => 'interval', 'intervalSeconds' => '120', 'seconds' => 120], MaterializationManifest::fromArray(['schedule' => ['policy' => 'INTERVAL', 'intervalSeconds' => '120']])->schedule);
		$this->assertSame(['interval_seconds' => 90, 'policy' => 'interval', 'seconds' => 90], MaterializationManifest::fromArray(['schedule' => ['interval_seconds' => 90]])->schedule);
		$this->assertSame(['policy' => 'interval', 'seconds' => 300], MaterializationManifest::fromArray(['schedule' => ['policy' => 'unknown', 'seconds' => 0]])->schedule);
		$this->assertSame(['policy' => 'interval', 'seconds' => 300], MaterializationManifest::fromArray(['schedule' => 'not-array'])->schedule);
	}

	public function testDailyAfterScheduleUsesValidClockFormatOrDefault(): void {
		$this->assertSame(['policy' => 'daily_after', 'time' => '09:45'], MaterializationManifest::fromArray(['schedule' => ['policy' => ' DAILY_AFTER ', 'time' => ' 09:45 ']])->schedule);
		$this->assertSame(['policy' => 'daily_after', 'time' => '02:00'], MaterializationManifest::fromArray(['schedule' => ['policy' => 'daily_after', 'time' => 'tomorrow']])->schedule);
		$this->assertSame(['policy' => 'daily_after', 'time' => '02:00'], MaterializationManifest::fromArray(['schedule' => ['policy' => 'daily_after']])->schedule);
	}

	public function testManualAndAlwaysSchedulesDoNotAddIntervalSeconds(): void {
		foreach (['manual', 'always'] as $policy) {
			$this->assertSame(['policy' => $policy], MaterializationManifest::fromArray(['schedule' => ['policy' => $policy]])->schedule);
		}
	}

	public function testSchemaHashTracksOnlySchemaRelevantFields(): void {
		$one = MaterializationManifest::fromArray([
			'id' => 'one', 'targetSchema' => 'reporting', 'logicalTable' => 'summary',
			'columns' => [['name' => 'count', 'type' => 'int']],
			'indexes' => [['columns' => ['count']]],
			'query' => ['select' => 'count'], 'priority' => 12
		]);
		$two = MaterializationManifest::fromArray([
			'id' => 'two', 'targetSchema' => 'reporting', 'logicalTable' => 'summary',
			'columns' => [['name' => 'count', 'type' => 'int']],
			'indexes' => [['columns' => ['count']]],
			'query' => ['select' => 'other'], 'priority' => 8
		]);
		$this->assertSame($one->getSchemaHash(), $two->getSchemaHash());
		$this->assertSame(64, strlen($one->getSchemaHash()));
		$two->indexes = [];
		$this->assertFalse($one->getSchemaHash() === $two->getSchemaHash());
		$two->indexes = $one->indexes;
		$two->columns = [];
		$this->assertFalse($one->getSchemaHash() === $two->getSchemaHash());
	}

	public function testQueryHashTracksOnlyQueryDefinition(): void {
		$one = MaterializationManifest::fromArray(['id' => 'a', 'query' => ['source' => 'users', 'locale' => 'München']]);
		$two = MaterializationManifest::fromArray(['id' => 'b', 'query' => ['source' => 'users', 'locale' => 'München'], 'targetSchema' => 'other']);
		$this->assertSame($one->getQueryHash(), $two->getQueryHash());
		$this->assertSame(hash('sha256', '{"source":"users","locale":"München"}'), $one->getQueryHash());
		$two->query = ['source' => 'courses'];
		$this->assertFalse($one->getQueryHash() === $two->getQueryHash());
	}

	public function testToArraySupportsReconstructionWithoutLosingNormalizedValues(): void {
		$manifest = MaterializationManifest::fromArray([
			'id' => 'users', 'source_schema' => 'public', 'target_schema' => 'analytics',
			'logical_table' => 'users', 'depends_on' => 'roles, accounts,roles',
			'schedule' => ['policy' => 'daily_after', 'time' => '12:00']
		]);
		$this->assertSame($manifest->toArray(), MaterializationManifest::fromArray($manifest->toArray())->toArray());
	}
}
