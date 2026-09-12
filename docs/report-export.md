# Report export contract

## Purpose

`ResourceFoundation\Api\IReportExporter` is the neutral BASE3 contract for transforming an already executed `QueryResult` into another representation.

The contract deliberately does not execute queries. Query execution stays behind `ResourceFoundation\Api\IQueryService`.

This separation keeps the dependency direction clear:

```text
query consumer
    |
    +--> IQueryService --> QueryResult
                         |
                         +--> IReportExporter --> string or file
```

A query implementation such as DataHawk can therefore be used without Vizion or any exporter implementation. A UI/reporting plugin such as Vizion can use any `IQueryService` implementation and any discoverable `IReportExporter` implementation.

## Contract

An exporter:

- implements `IReportExporter`
- has a stable technical `IBase::getName()` value
- receives data through `setResult(QueryResult $result)`
- returns the current result through `getResult()`
- serializes through `toString()`
- may persist the serialized result through `toFile()`
- declares its MIME type and file extension

The contract intentionally has no query method and no SQL method. SQL generation and query execution belong to the query layer.

## Discovery

Exporter implementations are discoverable BASE3 components. Consumers should use `IClassMap` directly:

```php
$exporter = $classMap->getInstanceByInterfaceName(
	IReportExporter::class,
	'csvreportexporter'
);
```

A separate exporter factory is unnecessary because `IClassMap` already owns discovery and instantiation.

To list all installed exporters:

```php
$exporters = $classMap->getInstancesByInterface(IReportExporter::class);
```

## Stable names

Configuration must use the exact `getName()` value of an exporter implementation. Do not introduce format aliases such as `csv` or `excel` next to the technical component names.

Example:

```json
{
  "exporters": [
    "csvreportexporter",
    "xlsxreportexporter",
    "jsonreportexporter"
  ]
}
```

Display labels such as `CSV` or `Excel` are UI concerns and are not component identities.

## Implementation ownership

ResourceFoundation contains only the shared contract and shared query result DTO. It does not choose or register final exporter implementations.

Implementations belong to plugins that own the output concern. Vizion currently provides the report exporters used by its report UI and by other consumers that need rendered report output.

## Adding an exporter

1. Implement `IReportExporter` in the plugin that owns the output representation.
2. Return a stable lowercase technical name from `getName()`.
3. Keep the implementation result-only. Do not inject or call `IQueryService` from the exporter.
4. Add tests for serialization, MIME type and extension.
5. Consumers can then discover the exporter through `IClassMap` and configure it using its exact `getName()` value.
