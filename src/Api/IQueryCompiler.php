<?php declare(strict_types=1);

namespace ResourceFoundation\Api;

use ResourceFoundation\Dto\QueryStatement;

/**
 * Generic compiler interface for transforming structured query definitions
 * into executable SQL query representations.
 */
interface IQueryCompiler {

	/**
	 * Compiles a structured query definition (as associative array)
	 * into a SQL-ready object representation.
	 *
	 * @param array $query Structured query definition
	 * @return QueryStatement Compiled SQL representation
	 */
	public function compile(array $query): QueryStatement;
}

