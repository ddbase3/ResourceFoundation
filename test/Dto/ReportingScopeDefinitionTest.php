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

namespace Base3\ResourceFoundation\Test\Dto;

use PHPUnit\Framework\TestCase;
use Base3\ResourceFoundation\Dto\ReportingScopeDefinition;

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
