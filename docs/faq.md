# ResourceFoundation FAQ

## What is ResourceFoundation?

ResourceFoundation is the shared BASE3 contract layer for resource-oriented data access. It defines interfaces, DTOs, exceptions, and proxy classes for entities, files, query services, query schemas, report export, materialization, access structures, relations, metadata, tags, profiles, activity data, and user-specific entity data.

ResourceFoundation is intentionally implementation-light. It defines service slots and transport structures, while concrete plugins or project composition decide which storage, database, remote service, or other backend implements those contracts.

## Does ResourceFoundation store data itself?

No general resource backend is implemented by ResourceFoundation itself. `ResourceFoundationPlugin` only registers the plugin instance in the BASE3 container.

Actual persistence belongs to implementations of the ResourceFoundation interfaces. Those implementations may use databases, files, object storage, remote APIs, microservices, or other backends.

## What is the main entity contract?

`IEntityDataService` defines generic entity CRUD operations:

- list entries
- load one entry
- create an entry
- update an entry
- delete an entry

The contract deliberately uses generic arrays and `int|string` identifiers so implementations are not tied to one database schema or storage technology.

## Which additional entity capabilities are defined?

ResourceFoundation separates optional entity concerns into focused contracts:

- `IEntityAccessService` for entry grants, roles, permissions, user roles, group roles, and group memberships
- `IEntityActivityService` for logs and comments
- `IEntityMetadataService` for shared key-value metadata
- `IEntityProfileService` for user-owned profiles and an active profile
- `IEntityRelationService` for relations between entities
- `IEntityStructureService` for types, modules, scopes, and their relations
- `IEntityTagService` for entry tags and tag definitions
- `IEntityUserDataService` for per-user values attached to an entity
- `IEntityFileService` for file entities and their associated content

This keeps unrelated resource concerns independent while allowing one backend to implement several of them.

## What is the difference between metadata and user data?

`IEntityMetadataService` describes data that belongs to the entity itself and is shared for that entry.

`IEntityUserDataService` describes values for one entity/user pair. The interface explicitly allows a concrete user ID or `null` for the current user. Typical uses include personal flags, UI state, favorites, or per-user markers.

## How are files represented?

ResourceFoundation separates file entities from file storage.

`IEntityFileService` models files as resource entities. Its normalized payload can include filename, Base64-encoded content, MIME type, size, name, description, caption-like content, preview data, tags, metadata, relations, and access information.

`IFileStorage` is the lower-level storage abstraction for listing, reading, writing, deleting, creating directories, removing directories, checking existence, and reading file metadata.

A concrete system can therefore keep file metadata in one backend and file bytes in another.

## What storage backends can implement `IFileStorage`?

The interface is storage-neutral. The source documentation names local filesystems, WebDAV, S3, FTP, and other backends as possible implementations. ResourceFoundation itself does not select one.

## What is `IQueryService`?

`IQueryService` is the generic structured-query boundary. It provides:

- visible table metadata
- metadata for one table
- structured query execution
- visible domains
- visible categories
- visible tags

`executeQuery()` accepts an array-based structured query and returns a `QueryResult` DTO.

The interface documentation expects the implementation to return only data accessible to the current user and declares `AccessDeniedException` and `QueryValidationException` as failure categories.

## Does ResourceFoundation execute SQL?

ResourceFoundation defines the contracts but does not provide a database query engine in this package.

`IQueryCompiler` converts a structured query definition into a `QueryStatement`. The DTO can contain SQL, bound parameters, selected-field metadata, a sensitivity flag, and a wildcard-query flag. A concrete implementation decides how this is compiled and executed.

## What does `QueryResult` contain?

`QueryResult` can contain:

- column metadata
- result rows
- optional debug SQL
- a result-level sensitivity flag
- affected row count for writes
- an insert ID for inserts

Column metadata can also mark individual result columns as sensitive.

## What do the `sensitive` flags do?

`FieldMetadata`, `TableMetadata`, `QueryStatement`, and `QueryResult` can carry sensitivity metadata.

These flags describe data. They do not by themselves encrypt, redact, authorize, or block access. A query implementation, UI, exporter, logger, or other consumer must decide how sensitivity metadata affects behavior.

## How are query schemas described?

`IQuerySchemaProvider` returns `TableMetadata` definitions. A table definition may include:

- name and label
- description
- domain and category
- tags
- fields
- joins
- default filters
- sensitivity metadata
- presentation position

`IScopedQuerySchemaProvider` extends this model with independent named schema scopes and a default scope.

## What are reporting scopes?

`IReportingScopeDefinitionProvider` and `IReportingScopeRegistry` provide user-facing reporting areas. A `ReportingScopeDefinition` can group one or more underlying query-schema scopes and report-definition scopes under a stable public identifier and label.

This separates user-facing reporting areas from internal provider names.

## What is `IReportConfigDefinitionProvider`?

It is the neutral contract for discoverable report definition datasets. Each provider owns one logical scope and returns named report datasets. Enabled datasets contain a report `definition` array.

The contract does not prescribe whether definitions come from files, settings, a database, or another source.

## What is `IReportExporter`?

`IReportExporter` transforms an already executed `QueryResult` into another representation.

It can:

- receive and expose a `QueryResult`
- render the result as a string
- write the result to a file
- expose a MIME type
- expose a recommended file extension

The exporter contract deliberately does not execute queries. Query execution remains behind `IQueryService`.

## Does ResourceFoundation provide concrete exporters?

No concrete exporter implementation is part of this package. ResourceFoundation only defines the shared exporter contract. Reporting or presentation plugins can provide concrete exporters discoverable through the BASE3 class map.

## What are materialization contracts for?

ResourceFoundation defines neutral contracts and DTOs for generated or materialized query tables.

Important types include:

- `MaterializationManifest`
- `MaterializationGeneration`
- `MaterializationRunResult`
- `IMaterializationService`
- `IMaterializationRegistry`
- `IMaterializationRunRepository`
- `IMaterializationManifestProvider`
- `IScopedMaterializationManifestProvider`
- `IMaterializationDefinitionProvider`
- `IMaterializationSchemaProvider`

The manifest can describe source and target schemas, logical and physical table naming, query definitions, columns, indexes, refresh options, dependencies, priority, dependency refresh behavior, and schedules.

## Does ResourceFoundation create materialized tables itself?

No. It defines the contracts and data structures. A concrete implementation is responsible for creating tables, publishing generations, recording runs, retaining or deleting generations, and applying schedules.

## What are the proxy classes?

ResourceFoundation contains proxies such as `EntityDataProxy`, `EntityAccessProxy`, and corresponding proxies for the other entity subservices.

These proxies implement ResourceFoundation interfaces and forward calls through `Base3\Microservice\Api\IMicroserviceConnector`. This allows a project to bind a ResourceFoundation service to a remote provider without changing the consumer contract.

## Does using ResourceFoundation automatically create a microservice connection?

No. The proxy classes only become active when project composition selects them and supplies a configured `IMicroserviceConnector`. Local implementations remain possible and are not replaced automatically.

## Where is access control enforced?

ResourceFoundation defines access-related contracts and query-service semantics, but it does not provide a universal authorization implementation.

The active implementation is responsible for enforcing access at the appropriate resource boundary. Consumers should not treat the existence of `IEntityAccessService`, sensitivity flags, or query schema metadata as proof that a particular backend has already applied an access decision.

## Does ResourceFoundation authenticate users?

No. It does not implement login, sessions, authentication middleware, or a user directory. Contracts may carry user IDs and group or role information, but identity establishment belongs to the active host or implementation.

## Does ResourceFoundation log requests or resource data?

The package does not register a logger and does not contain a general logging implementation for these contracts. Concrete implementations and consumers can log their own operations.

## Does ResourceFoundation define data-retention periods?

No. Retention depends on the concrete backend and the application using it. The contracts expose create, update, delete, archive, materialization, and file deletion operations, but they do not define organization-specific retention periods.

## Can ResourceFoundation be used without a database?

Yes. Nothing in the foundation requires a database-backed implementation. A project can bind file-based, in-memory, remote, or other implementations where the interface semantics can be satisfied.

## How should a reusable plugin depend on ResourceFoundation?

A reusable plugin should type-hint the relevant ResourceFoundation API and receive it through constructor injection. It should not directly select a concrete resource backend when replacement is expected.

Final implementation selection belongs in project composition or a custom bootstrap.

## Where can I find the report export contract documentation?

See [report-export.md](report-export.md) for the separation between query execution and result export.
