<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Proxy;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Proxy\EntityTagProxy;
use RuntimeException;

final class EntityTagProxyTest extends TestCase {

	public function testEntryTagsAndCatalogQueriesReturnDataAsIs(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['getEntryTags'] = ['internal', 'published'];
		$connector->responses['getTags'] = [['tag' => 'internal']];
		$connector->responses['getModuleTags'] = [['tag' => 'published']];
		$proxy = new EntityTagProxy($connector);
		$this->assertSame(['internal', 'published'], $proxy->getEntryTags('entry-10'));
		$this->assertSame([['tag' => 'internal']], $proxy->getTags('course'));
		$this->assertSame([['tag' => 'published']], $proxy->getModuleTags('learning'));
		$this->assertSame([
			['getEntryTags', ['entry-10']],
			['getTags', ['course']], ['getModuleTags', ['learning']]
		], $connector->calls);
	}

	public function testMissingTagQueriesReturnEmptyLists(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityTagProxy($connector);
		$this->assertSame([], $proxy->getEntryTags(10));
		$this->assertSame([], $proxy->getTags());
		$this->assertSame([], $proxy->getModuleTags());
		$this->assertSame([['getEntryTags', [10]], ['getTags', [null]], ['getModuleTags', [null]]], $connector->calls);
	}

	public function testTagAssignmentsToEntryAreDelegatedWithoutTransformingTags(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityTagProxy($connector);
		$proxy->addEntryTags(1, ['urgent', 'internal']);
		$proxy->removeEntryTags(1, ['internal']);
		$proxy->replaceEntryTags(1, []);
		$this->assertSame([
			['addEntryTags', [1, ['urgent', 'internal']]],
			['removeEntryTags', [1, ['internal']]],
			['replaceEntryTags', [1, []]]
		], $connector->calls);
	}

	public function testTagDefinitionsAndScopeAssignmentsAreDelegated(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityTagProxy($connector);
		$proxy->describeTag('internal', 'Not public');
		$proxy->assignTagToScope('internal', 'private');
		$proxy->removeTagFromScope('internal', 'public');
		$this->assertSame([
			['describeTag', ['internal', 'Not public']],
			['assignTagToScope', ['internal', 'private']],
			['removeTagFromScope', ['internal', 'public']]
		], $connector->calls);
	}

	public function testTagModuleAssignmentsAreDelegatedSeparately(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityTagProxy($connector);
		$proxy->assignTagToModule('selected', 'reports');
		$proxy->removeTagFromModule('selected', 'reports');
		$this->assertSame([
			['assignTagToModule', ['selected', 'reports']],
			['removeTagFromModule', ['selected', 'reports']]
		], $connector->calls);
	}

	public function testTagChangesPropagateBackendErrors(): void {
		$connector = new RecordingEntityConnector();
		$connector->failures['replaceEntryTags'] = new RuntimeException('backend error');
		$this->expectException(RuntimeException::class);
		(new EntityTagProxy($connector))->replaceEntryTags(1, ['test']);
	}
}
