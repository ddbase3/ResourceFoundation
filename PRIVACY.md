# Privacy and data processing in ResourceFoundation

This document describes privacy-relevant behavior of the ResourceFoundation component itself. ResourceFoundation is primarily a contract, DTO, exception, and proxy package. It does not define the complete privacy behavior of a concrete resource backend.

## Component role

ResourceFoundation defines common interfaces for entity data, file data, query services, query schemas, report export, materialization, resource access, relations, metadata, tags, profiles, activity information, and per-user resource data.

Most interfaces operate on generic arrays or DTOs because the foundation must remain independent of a particular database schema or storage technology.

The final runtime decides which implementations are bound to these interfaces.

## Data ResourceFoundation contracts can carry

Depending on the active implementation and application domain, ResourceFoundation contracts can carry personal data or other sensitive information.

Examples include:

- entity records and arbitrary entity fields
- user identifiers and group identifiers
- roles and permissions
- direct user and group access grants
- user-group and role assignments
- comments and activity-log records
- user-owned profiles
- per-user entity data
- entity relations
- tags and structured metadata
- file names, MIME types, descriptions, previews, file content, and file metadata
- query definitions and query parameters
- query-result rows
- table and field metadata
- report definitions
- materialization definitions, generation metadata, run metadata, and row counts

The foundation does not restrict these generic payloads to non-personal information.

## No general persistence in the foundation plugin

`ResourceFoundationPlugin` only registers the plugin instance in the BASE3 container.

ResourceFoundation does not provide the final entity database, file store, query database, materialization repository, or report-definition store in this package. Persistence behavior therefore depends on the selected implementation.

When an implementation stores data, that implementation must document its storage locations, retention rules, deletion behavior, backup implications, and access controls.

## Entity data

`IEntityDataService` permits listing, loading, creating, updating, and deleting generic resource entities. Entity payloads are arrays and may therefore contain any data defined by the active domain.

ResourceFoundation does not automatically minimize, redact, pseudonymize, or classify individual entity values. Those responsibilities belong to the implementation and the calling application.

## Access and authorization data

`IEntityAccessService` can process:

- direct entry grants for users and groups
- role definitions
- permission definitions
- role-permission assignments
- roles assigned to users
- roles assigned to groups
- group membership of users
- effective user roles

These structures can reveal organizational relationships and authorization information.

The existence of this contract does not create a universal authorization layer. A concrete backend must enforce the access model it exposes.

## Profiles and per-user data

`IEntityProfileService` uses user identifiers and supports user-owned profiles and an active-profile assignment.

`IEntityUserDataService` stores values for one entity/user pair. The contract explicitly supports personal flags, UI state, favorites, and similar values.

Concrete implementations must determine whether these values are personal data, where they are stored, and how they are deleted when a user or entity is removed.

## Activity logs and comments

`IEntityActivityService` defines append-style logs and mutable comments. Log operations can include a user identifier, and comments may contain arbitrary text.

ResourceFoundation does not impose a retention period or content filter for activity logs and comments. Implementations should define retention and deletion policies appropriate to their domain.

## Metadata, tags, relations, and structure

Metadata and relation APIs are intentionally generic. Metadata values may contain personal information if the application stores such information there. Relations can reveal associations between records. Tags and structural definitions may reveal classification or organizational metadata.

ResourceFoundation does not automatically treat these values as anonymous.

## File data

`IEntityFileService` supports file payloads containing Base64-encoded content and metadata. `getFileContent()` can return either Base64 or raw content, depending on options.

`IFileStorage` can read and write arbitrary file content and expose file metadata through `stat()`.

File content can contain personal or confidential data. The concrete storage implementation is responsible for storage location, transport security, encryption if required, access enforcement, backup behavior, retention, and deletion.

## Query metadata and result data

`IQueryService` and related DTOs can expose table metadata, field metadata, query results, and structured query behavior.

`QueryResult` may contain:

- arbitrary result rows
- column metadata
- an optional `debugSql` string
- sensitivity metadata
- affected row counts
- insert identifiers

A query result can therefore contain personal or sensitive data even when the query definition itself is technical.

## Sensitivity metadata

`FieldMetadata`, `TableMetadata`, `QueryStatement`, and `QueryResult` can mark data as sensitive.

The `sensitive` property is descriptive metadata only. ResourceFoundation does not automatically:

- hide sensitive fields
- redact values
- block export
- prevent logging
- encrypt values
- enforce authorization

Consumers and implementations must apply the required policy at their own boundary.

## Debug SQL

`QueryResult` can contain `debugSql`, and `QueryStatement` contains the compiled SQL statement and bound parameters separately.

Depending on the concrete query implementation, debug SQL can reveal table names, field names, filter values, identifiers, or other query context. Consumers should avoid exposing or logging debug SQL unless required for an authorized operational purpose.

ResourceFoundation itself does not log `debugSql`.

## Query access control

The `IQueryService` contract states that visible tables and query results are for the current user and declares `AccessDeniedException` as an expected failure category.

The actual access decision is implementation-specific. ResourceFoundation does not authenticate the current user and does not independently verify a query implementation's authorization behavior.

## Schema metadata

Query schema DTOs can reveal table names, field names, types, foreign-key relations, joins, domains, categories, tags, and sensitivity classifications.

Schema metadata may itself be security-relevant even when it does not contain row data. Applications should expose schema discovery only to contexts that are intended to receive it.

## Report definitions and exports

`IReportConfigDefinitionProvider` can expose report definitions. Those definitions may reference schemas, tables, fields, filters, and other domain structures.

`IReportExporter` can transform a `QueryResult` and write the transformed content to a file. ResourceFoundation contains the contract, not the concrete exporters. Export content, output paths, temporary-file handling, download behavior, and cleanup are responsibilities of the exporter implementation and calling application.

## Materialization data

Materialization DTOs and interfaces can describe generated tables and their lifecycle. A `MaterializationManifest` can contain structured queries, column definitions, indexes, refresh configuration, dependencies, options, and schedules. Generation and run records can contain table names, hashes, statuses, timestamps, row counts, messages, and arbitrary metadata.

ResourceFoundation does not itself create or retain materialized copies. A concrete materialization implementation must document whether materialized tables duplicate personal data, how long generations are retained, and how deleted or changed source data is propagated.

## Microservice proxies

ResourceFoundation includes entity service proxies backed by `IMicroserviceConnector`. If a project binds one of these proxies as the active service, method arguments and results can cross a service or network boundary according to the connector configuration.

The proxy layer does not itself choose a remote endpoint, transport encryption, authentication mechanism, processing region, or retention policy. Those properties belong to the configured microservice infrastructure.

When proxies are used, the installation should document:

- which ResourceFoundation services are remote
- which fields or files cross the boundary
- transport security
- service authentication
- remote storage and logging
- processing location
- retention and deletion behavior

## Authentication and sessions

ResourceFoundation does not implement login, session management, cookies, or request authentication. Some contracts accept user IDs or refer to the current user semantically, but identity establishment is external to this component.

## Logging

ResourceFoundation does not register a general logger and does not automatically log entity payloads, file content, queries, or result rows.

Implementations and consumers may log such data. Their logging policy should be assessed separately, especially for comments, user identifiers, file names, query values, and debug SQL.

## Network communication

ResourceFoundation does not automatically contact external systems merely by being installed.

Network communication becomes possible when a selected implementation uses a remote file store, remote data service, microservice proxy, or other network-backed service.

## Retention and deletion

ResourceFoundation defines operations that can support deletion or archival, but it does not define global retention periods.

A concrete deployment should define retention and deletion for at least:

- entities
- archived roles and permissions
- direct access grants
- comments and activity logs
- profiles
- per-user entity data
- metadata and relations
- files and file metadata
- query-related logs in implementation components
- exported files
- materialized generations and run history
- remote service copies
- backups

## Security and privacy responsibilities of implementations

Concrete ResourceFoundation implementations should document and test, where applicable:

- authentication and authorization
- per-user and per-group access enforcement
- data minimization
- sensitivity handling
- validation of structured queries
- write authorization
- file access controls
- transport security
- secret handling
- logging behavior
- backup behavior
- retention and deletion
- microservice boundaries

ResourceFoundation provides the stable contracts. It does not replace these implementation responsibilities.
