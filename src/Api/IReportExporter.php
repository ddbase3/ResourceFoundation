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

namespace ResourceFoundation\Api;

use Base3\Api\IBase;
use ResourceFoundation\Dto\QueryResult;

/**
 * Converts an already executed QueryResult into an export representation.
 *
 * Exporters deliberately do not execute queries themselves. Query execution
 * stays behind IQueryService, while exporter implementations only transform
 * the result they receive. Implementations are discoverable through IClassMap
 * by their stable IBase::getName() value.
 */
interface IReportExporter extends IBase {

	/**
	 * Sets the result to export.
	 */
	public function setResult(QueryResult $result): self;

	/**
	 * Returns the currently assigned result.
	 */
	public function getResult(): ?QueryResult;

	/**
	 * Renders the export content as a string.
	 */
	public function toString(): string;

	/**
	 * Writes the export content to a file.
	 *
	 * @throws \RuntimeException If writing fails
	 */
	public function toFile(string $filePath): self;

	/**
	 * Returns the MIME type of the exported representation.
	 */
	public function getMimeType(): string;

	/**
	 * Returns the recommended file extension without a leading dot.
	 */
	public function getFileExtension(): string;
}
