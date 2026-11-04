<?php declare(strict_types=1);

namespace ResourceFoundation\Test\Dto;

use PHPUnit\Framework\TestCase;
use ResourceFoundation\Dto\ReportingScopeDefinition;

final class ReportingScopeDefinitionTest extends TestCase {

	public function testDefaultScopeHasNoTechnicalProviders(): void {
		$scope = new ReportingScopeDefinition('public', 'Public');
		$this->assertSame('public', $scope->id);
		$this->assertSame('Public', $scope->label);
		$this->assertSame([], $scope->querySchemaScopes);
		$this->assertSame([], $scope->materializationScopes);
		$this->assertSame([], $scope->reportScopes);
	}

	public function testScopeKeepsIndependentProviderGroupsWithoutMergingThem(): void {
		$scope = new ReportingScopeDefinition('internal', 'Internal reports', ['finance', 'hr'], ['materialized-reports'], ['manager']);
		$this->assertSame(['finance', 'hr'], $scope->querySchemaScopes);
		$this->assertSame(['materialized-reports'], $scope->materializationScopes);
		$this->assertSame(['manager'], $scope->reportScopes);
	}
}
