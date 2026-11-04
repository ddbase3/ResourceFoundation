<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Proxy;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Proxy\EntityStructureProxy;
use RuntimeException;

final class EntityStructureProxyTest extends TestCase {

	public function testTypeOperationsPreserveTypesAndCallerOptions(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['getTypes'] = [['id' => 'article']];
		$connector->responses['getType'] = ['id' => 'article', 'label' => 'Article'];
		$connector->responses['createType'] = 'new-type';
		$proxy = new EntityStructureProxy($connector);
		$this->assertSame([['id' => 'article']], $proxy->getTypes());
		$this->assertSame(['id' => 'article', 'label' => 'Article'], $proxy->getType('article'));
		$this->assertSame('new-type', $proxy->createType(['label' => 'Article']));
		$proxy->updateType('new-type', ['label' => 'Updated']);
		$this->assertSame([
			['getTypes', []], ['getType', ['article']],
			['createType', [['label' => 'Article']]],
			['updateType', ['new-type', ['label' => 'Updated']]]
		], $connector->calls);
	}

	public function testModuleOperationsIncludeCreateUpdateAndFind(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['getModules'] = [['name' => 'courses']];
		$connector->responses['getModule'] = ['name' => 'courses'];
		$connector->responses['createModule'] = 'module-123';
		$proxy = new EntityStructureProxy($connector);
		$this->assertSame([['name' => 'courses']], $proxy->getModules());
		$this->assertSame(['name' => 'courses'], $proxy->getModule('courses'));
		$this->assertSame('module-123', $proxy->createModule(['name' => 'courses']));
		$proxy->updateModule('module-123', ['label' => 'Courses']);
		$this->assertSame([
			['getModules', []], ['getModule', ['courses']],
			['createModule', [['name' => 'courses']]],
			['updateModule', ['module-123', ['label' => 'Courses']]]
		], $connector->calls);
	}

	public function testScopeOperationsRetainScopeAndModuleIdentities(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['getScopes'] = [['name' => 'public']];
		$connector->responses['getScope'] = ['name' => 'public'];
		$connector->responses['createScope'] = 'new-scope';
		$proxy = new EntityStructureProxy($connector);
		$this->assertSame([['name' => 'public']], $proxy->getScopes());
		$this->assertSame(['name' => 'public'], $proxy->getScope('public'));
		$this->assertSame('new-scope', $proxy->createScope(['name' => 'public']));
		$proxy->assignModuleToScope('courses', 'public');
		$proxy->removeModuleFromScope('courses', 'public');
		$this->assertSame([
			['getScopes', []], ['getScope', ['public']], ['createScope', [['name' => 'public']]],
			['assignModuleToScope', ['courses', 'public']],
			['removeModuleFromScope', ['courses', 'public']]
		], $connector->calls);
	}

	public function testScopeModuleQueriesKeepOptionalScopeAndModuleFilters(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['getScopeModules'] = ['courses'];
		$connector->responses['getModuleScopes'] = ['public', 'private'];
		$proxy = new EntityStructureProxy($connector);
		$this->assertSame(['courses'], $proxy->getScopeModules('public'));
		$this->assertSame(['public', 'private'], $proxy->getModuleScopes('courses'));
		$this->assertSame([['getScopeModules', ['public']], ['getModuleScopes', ['courses']]], $connector->calls);
	}

	public function testNullReadsUseExpectedArrayOrNullableFallbacks(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityStructureProxy($connector);
		$this->assertSame([], $proxy->getTypes());
		$this->assertNull($proxy->getType(1));
		$this->assertSame([], $proxy->getModules());
		$this->assertNull($proxy->getModule('unknown'));
		$this->assertSame([], $proxy->getScopes());
		$this->assertNull($proxy->getScope('unknown'));
		$this->assertSame([], $proxy->getScopeModules());
		$this->assertSame([], $proxy->getModuleScopes());
		$this->assertSame([
			['getTypes', []], ['getType', [1]], ['getModules', []], ['getModule', ['unknown']],
			['getScopes', []], ['getScope', ['unknown']],
			['getScopeModules', [null]], ['getModuleScopes', [null]]
		], $connector->calls);
	}

	public function testNullCreatesReturnTypedEmptyIdentifiers(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityStructureProxy($connector);
		$this->assertSame(0, $proxy->createType([]));
		$this->assertSame('', $proxy->createModule([]));
		$this->assertSame('', $proxy->createScope([]));
	}

	public function testStructureMutationFailureIsNotSilentlyIgnored(): void {
		$connector = new RecordingEntityConnector();
		$connector->failures['assignModuleToScope'] = new RuntimeException('permission denied');
		$this->expectException(RuntimeException::class);
		(new EntityStructureProxy($connector))->assignModuleToScope('courses', 'private');
	}
}
