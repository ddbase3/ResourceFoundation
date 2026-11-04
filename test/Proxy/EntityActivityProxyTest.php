<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Proxy;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Proxy\EntityActivityProxy;
use RuntimeException;

final class EntityActivityProxyTest extends TestCase {

	public function testLogQueriesPassOptionsAndPreserveResults(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['getLogs'] = [['id' => 1, 'action' => 'create']];
		$proxy = new EntityActivityProxy($connector);
		$this->assertSame([['id' => 1, 'action' => 'create']], $proxy->getLogs('entry-1', ['limit' => 5]));
		$this->assertSame([['getLogs', ['entry-1', ['limit' => 5]]]], $connector->calls);
	}

	public function testCommentQueriesPreserveIdsAndReturnData(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['getComments'] = [['id' => 4, 'message' => 'hello']];
		$proxy = new EntityActivityProxy($connector);
		$this->assertSame([['id' => 4, 'message' => 'hello']], $proxy->getComments(9, ['includeArchived' => false]));
		$this->assertSame([['getComments', [9, ['includeArchived' => false]]]], $connector->calls);
	}

	public function testAbsentLogAndCommentListsReturnEmptyArrays(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityActivityProxy($connector);
		$this->assertSame([], $proxy->getLogs(1));
		$this->assertSame([], $proxy->getComments(2));
		$this->assertSame([['getLogs', [1, []]], ['getComments', [2, []]]], $connector->calls);
	}

	public function testAddCommentKeepsParentAndReturnsRemoteId(): void {
		$connector = new RecordingEntityConnector();
		$connector->responses['addComment'] = 'comment-7';
		$proxy = new EntityActivityProxy($connector);
		$this->assertSame('comment-7', $proxy->addComment(15, 'reply', 4));
		$this->assertSame([['addComment', [15, 'reply', 4]]], $connector->calls);
	}

	public function testMissingCommentIdFallsBackToZero(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityActivityProxy($connector);
		$this->assertSame(0, $proxy->addComment(15, 'first'));
		$this->assertSame([['addComment', [15, 'first', null]]], $connector->calls);
	}

	public function testLogAndCommentMutationsAreForwardedInOrder(): void {
		$connector = new RecordingEntityConnector();
		$proxy = new EntityActivityProxy($connector);
		$proxy->addLog('entry', 'read', 11);
		$proxy->updateComment(27, 'revised');
		$proxy->deleteComment('comment-28');
		$this->assertSame([
			['addLog', ['entry', 'read', 11]],
			['updateComment', [27, 'revised']],
			['deleteComment', ['comment-28']]
		], $connector->calls);
	}

	public function testConnectorFailureIsNotConvertedToSuccess(): void {
		$connector = new RecordingEntityConnector();
		$connector->failures['addComment'] = new RuntimeException('write failed');
		$proxy = new EntityActivityProxy($connector);
		$this->expectException(RuntimeException::class);
		$proxy->addComment(1, 'cannot save');
	}
}

require_once __DIR__ . '/RecordingEntityConnector.php';
