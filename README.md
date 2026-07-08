# ResourceFoundation

**ResourceFoundation** is a core component of the BASE3 framework that provides stable resource contracts for entity data, file data, queryable schemas, materialization, metadata-rich resource access, and microservice-ready service boundaries.

It does not choose final implementations. It defines replaceable service slots.
Implementation plugins such as Memora provide concrete services, while consumer plugins depend on these interfaces.
Project plugins or custom bootstraps decide which implementation is active at runtime.

---

## Table of Contents

* [Overview](#overview)
* [Architecture Role](#architecture-role)
* [Core Concepts](#core-concepts)
	* [Entities](#entities)
	* [Files](#files)
	* [Relations](#relations)
	* [Metadata](#metadata)
	* [Tags](#tags)
	* [Profiles](#profiles)
	* [Access](#access)
	* [Structure](#structure)
	* [Activity](#activity)
	* [User Data](#user-data)
* [Key Interfaces](#key-interfaces)
	* [`IEntityDataService`](#ientitydataservice)
	* [`IEntityFileService`](#ientityfileservice)
	* [`IFileStorage`](#ifilestorage)
	* [`IQueryService`](#iqueryservice)
	* [`IQuerySchemaProvider`](#iqueryschemaprovider)
	* [`IQueryCompiler`](#iquerycompiler)
	* [Materialization Interfaces](#materialization-interfaces)
* [Expanded Entity Service Contracts](#expanded-entity-service-contracts)
	* [`IEntityProfileService`](#ientityprofileservice)
	* [`IEntityAccessService`](#ientityaccessservice)
	* [`IEntityRelationService`](#ientityrelationservice)
	* [`IEntityMetadataService`](#ientitymetadataservice)
	* [`IEntityTagService`](#ientitytagservice)
	* [`IEntityStructureService`](#ientitystructureservice)
	* [`IEntityActivityService`](#ientityactivityservice)
	* [`IEntityUserDataService`](#ientityuserdataservice)
* [Proxy Layer](#proxy-layer)
* [Implementation Responsibility](#implementation-responsibility)
* [Integration Examples](#integration-examples)
	* [Memora as XRM Backend](#memora-as-xrm-backend)
	* [WebDAV / Nextcloud / File Storage](#webdav--nextcloud--file-storage)
	* [Microservice Deployment](#microservice-deployment)
* [Layer Integration](#layer-integration)
* [Extensibility](#extensibility)
* [Design Rules](#design-rules)
* [Summary](#summary)

---

## Overview

ResourceFoundation defines common interfaces and DTOs for loading, querying, saving, and managing resources.
A resource may be a structured entity, a file entity, a physical or virtual file, a queryable table, a generated/materialized table, or an entity subsystem such as access, tags, metadata, profiles, comments, or user-specific data.

The foundation exists so that other BASE3 plugins can work with resources without knowing the concrete backend.
For example, an assistant plugin can read entity data, a CRM plugin can manage tags and metadata, and a website project can expose the same services through microservices.
All of these consumers should depend on ResourceFoundation contracts rather than on Memora classes, SQL table names, or project-specific services.

---

## Architecture Role

ResourceFoundation is a foundation plugin.
That means it defines stable shared contracts and transport-safe DTOs, but it should not normally contain final storage decisions.

The intended dependency direction is:

```text
Consumer plugin
  -> ResourceFoundation\Api interface
    -> implementation selected by project composition
```

Concrete examples:

```text
Memora                  implements ResourceFoundation entity contracts
Base3XrmWebsite          exposes selected contracts as microservices
Project plugins          may bind proxies instead of local implementations
Feature plugins          depend on ResourceFoundation, not on Memora
```

This follows the BASE3 rule:

```text
Known services belong in the container.
Discoverable components belong in the class map.
Final choices belong in project plugins or custom bootstraps.
```

ResourceFoundation interfaces are known service slots and are intended to be registered in the container.

---

## Core Concepts

### Entities

Entities are logical data objects addressed through `IEntityDataService`.
They are not tied to one specific table layout.
A concrete backend may store them in one table, multiple tables, document storage, remote APIs, or another service.

Typical entity operations are:

```text
list entries
load one entry
create an entry
patch/update an entry
delete or archive an entry
load optional aspects such as tags, metadata, relations, access, or typed data
```

### Files

Files are represented in two layers:

```text
IEntityFileService  -> file entities and their metadata
IFileStorage        -> physical or virtual file contents
```

This split allows a backend to store file metadata as an entity while storing file bytes in a local directory, WebDAV storage, S3, Nextcloud, or another adapter.

### Relations

Relations connect one entity to other entities.
ResourceFoundation calls this concept relation-level access through `IEntityRelationService`.
A backend such as Memora may implement this through allocation tables, link tables, or graph structures.

### Metadata

Metadata is entry-wide structured data.
It is suitable for external IDs, technical markers, import provenance, structured attributes, or backend-owned information that belongs to the entity as a whole.

### Tags

Tags are lightweight classifications.
They can classify entries, support module-like behavior, and be described or scoped by a concrete backend.

### Profiles

Profiles represent user-specific resource views or query settings.
They are useful for saved filters, active working contexts, table column presets, and other user-owned resource presentation state.

### Access

Access is the boundary around users, groups, roles, grants, and memberships.
ResourceFoundation does not define the table layout. It defines service methods for managing access-related resource structures.

### Structure

Structure covers reusable resource definitions such as types, modules, scopes, and module-scope relations.
It describes the semantic resource model rather than a single entity instance.

### Activity

Activity covers append-style logs and mutable comments attached to entries.
It is intentionally separate from metadata because logs and comments have their own lifecycle.

### User Data

User data is per-user data attached to an entry.
It is not the same as metadata.

```text
metadata       = shared entry-wide data
user data      = user-specific entry data
```

---

## Key Interfaces

### `IEntityDataService`

Generic CRUD service for entity-like resources.

```php
public function getEntries(array $options = []): array;
public function getEntry(int|string $id, array $options = []): ?array;
public function createEntry(array $data): int|string;
public function updateEntry(int|string $id, array $patch): int|string;
public function deleteEntry(int|string $id): bool;
```

This interface is the main generic entity boundary.
Concrete implementations may support additional load options, filter options, create verbs, or update verbs, but consumers should keep those options explicit and documented by the implementation they target.

Example:

```php
use ResourceFoundation\Api\IEntityDataService;

$entry = $entityDataService->getEntry(42, [
	'loadname' => true,
	'loadtags' => true,
	'loadmetadata' => true
]);
```

### `IEntityFileService`

Generic service for file entities.

```php
public function createFile(array $file, array $options = []): array;
public function replaceFile(int|string $id, array $file, array $options = []): array;
public function getFile(int|string $id, array $options = []): ?array;
public function getFileContent(int|string $id, array $options = []): ?string;
public function deleteFile(int|string $id, array $options = []): bool;
```

This interface is used when a file is more than raw bytes and should also exist as an entity with metadata, tags, relations, or access.

### `IFileStorage`

Generic file storage interface independent of entity metadata.

```php
public function list(string $path = ''): array;
public function read(string $path): string;
public function write(string $path, string $content): bool;
public function delete(string $path): bool;
public function mkdir(string $path): bool;
public function rmdir(string $path): bool;
public function exists(string $path): bool;
public function stat(string $path): ?array;
```

`IFileStorage` can be implemented by local storage, WebDAV, Nextcloud, S3, memory-backed storage, or project-specific storage.

### `IQueryService`

Generic query service for schema-aware table access.

```php
public function listTables(): array;
public function getTable(string $tableName): ?TableMetadata;
public function executeQuery(array $queryJson): QueryResult;
public function listDomains(): array;
public function listCategories(): array;
public function listTags(): array;
```

The method `executeQuery()` receives a structured query array and returns a `QueryResult` DTO.
This keeps query construction portable and allows implementations to validate query definitions against schema metadata.

### `IQuerySchemaProvider`

Schema provider for queryable data.

```php
public function getSchema(): array;
public function getTable(string $tableName): ?TableMetadata;
```

Implementations expose table metadata, field metadata, joins, foreign keys, domains, categories, and tags.

### `IQueryCompiler`

Compiler interface for transforming structured query definitions into executable query statements.

```php
public function compile(array $query): QueryStatement;
```

The compiler is usually an implementation detail of a concrete query service.
Consumers should normally depend on `IQueryService`, not on `IQueryCompiler`.

### Materialization Interfaces

ResourceFoundation also contains materialization contracts for generated query structures:

```text
IMaterializationService
IMaterializationManifestProvider
IMaterializationSchemaProvider
IMaterializationRegistry
IMaterializationRunRepository
```

These contracts are used when data needs to be built, refreshed, published, or queried as generated tables.
They are separate from the normal entity service layer.

---

## Expanded Entity Service Contracts

The expanded service contracts model common entity subsystems without exposing implementation tables.
They are intentionally backend-neutral.
A backend may be SQL-based, remote, file-based, graph-based, or proxied through BASE3 microservices.

### `IEntityProfileService`

User-specific resource profiles.

```php
public function getActiveProfile(?int $userId = null): ?array;
public function getProfiles(?int $userId = null, bool $includeArchived = false): array;
public function createProfile(int $userId, array $profile): int|string;
public function updateProfile(int|string $profileId, array $patch): void;
public function archiveProfile(int|string $profileId): void;
public function setActiveProfile(int $userId, int $profileId): void;
```

Use this for saved filters, active profiles, default views, and user-specific entity list configuration.

### `IEntityAccessService`

Access management for entries, users, groups, roles, and memberships.

```php
public function getEntryAccess(int|string $entryId): array;
public function replaceEntryUserAccess(int|string $entryId, array $access): void;
public function replaceEntryGroupAccess(int|string $entryId, array $access): void;
public function replaceEntryRoleAccess(int|string $entryId, array $access): void;
public function getRoles(bool $includeArchived = false): array;
public function getRole(int|string $roleId): ?array;
public function createRole(array $role): int|string;
public function updateRole(int|string $roleId, array $patch): void;
public function archiveRole(int|string $roleId): void;
public function getUserRoles(int|string $userId): array;
public function getGroupRoles(int|string $groupId): array;
public function getEffectiveUserRoles(int|string $userId): array;
public function replaceUserRoles(int|string $userId, array $roleIds): void;
public function replaceGroupRoles(int|string $groupId, array $roleIds): void;
public function getUserGroups(int|string $userId): array;
public function replaceUserGroups(int|string $userId, array $groupIds): void;
```

This interface separates entry-level grants from reusable roles.
The exact role model belongs to the implementation, but the public operations remain stable.

### `IEntityRelationService`

Entity-to-entity relation management.

```php
public function getRelations(int|string $entryId): array;
public function getRelationIds(int|string $entryId): array;
public function addRelations(int|string $entryId, array $peerIds): void;
public function removeRelations(int|string $entryId, array $peerIds): void;
public function replaceRelations(int|string $entryId, array $peerIds): void;
```

Use this when a consumer needs to manage relation lists without knowing the backend's relation table structure.

### `IEntityMetadataService`

Entry-wide metadata.

```php
public function getMetadata(int|string $entryId): array;
public function getMetadataValue(int|string $entryId, string $name, mixed $default = null): mixed;
public function setMetadata(int|string $entryId, array $metadata): void;
public function removeMetadata(int|string $entryId, array $names): void;
public function replaceMetadata(int|string $entryId, array $metadata): void;
```

Use this for shared, entry-wide metadata.

### `IEntityTagService`

Entry tag and tag catalog management.

```php
public function getEntryTags(int|string $entryId): array;
public function addEntryTags(int|string $entryId, array $tags): void;
public function removeEntryTags(int|string $entryId, array $tags): void;
public function replaceEntryTags(int|string $entryId, array $tags): void;
public function getTags(?string $scope = null): array;
public function describeTag(string $tag, string $description): void;
public function assignTagToScope(string $tag, string $scope): void;
public function removeTagFromScope(string $tag, string $scope): void;
public function assignTagToModule(string $tag, string $module): void;
public function removeTagFromModule(string $tag, string $module): void;
```

Use this for lightweight classification and tag catalog work.

### `IEntityStructureService`

Resource structure and semantic definitions.

```php
public function getTypes(): array;
public function getType(int|string $type): ?array;
public function createType(array $type): int|string;
public function updateType(int|string $typeId, array $patch): void;
public function getModules(): array;
public function getModule(string $module): ?array;
public function createModule(array $module): string;
public function updateModule(string $module, array $patch): void;
public function getScopes(): array;
public function getScope(string $scope): ?array;
public function createScope(array $scope): string;
public function assignModuleToScope(string $module, string $scope): void;
public function removeModuleFromScope(string $module, string $scope): void;
```

Use this for types, modules, scopes, and module-scope relationships.

### `IEntityActivityService`

Logs and comments attached to entries.

```php
public function getLogs(int|string $entryId, array $options = []): array;
public function addLog(int|string $entryId, string $action, ?int $userId = null): void;
public function getComments(int|string $entryId, array $options = []): array;
public function addComment(int|string $entryId, string $comment, ?int $parentId = null): int|string;
public function updateComment(int|string $commentId, string $comment): void;
public function deleteComment(int|string $commentId): void;
```

Use this for audit-like entry history and user comments.

### `IEntityUserDataService`

Per-user entry data.

```php
public function getUserData(int|string $entryId, ?int $userId = null): array;
public function getUserDataValue(int|string $entryId, string $name, mixed $default = null, ?int $userId = null): mixed;
public function setUserData(int|string $entryId, array $data, ?int $userId = null): void;
public function removeUserData(int|string $entryId, array $names, ?int $userId = null): void;
```

Use this for user-specific resource state such as pinned flags, UI state, per-user notes, or personal markers.

---

## Proxy Layer

ResourceFoundation includes proxy classes for microservice-based deployments.
A proxy implements the same ResourceFoundation interface as the local service, but delegates calls to a microservice connector object.

Proxy classes:

```text
ResourceFoundation\Proxy\EntityDataProxy
ResourceFoundation\Proxy\EntityFileProxy
ResourceFoundation\Proxy\EntityProfileProxy
ResourceFoundation\Proxy\EntityAccessProxy
ResourceFoundation\Proxy\EntityRelationProxy
ResourceFoundation\Proxy\EntityMetadataProxy
ResourceFoundation\Proxy\EntityTagProxy
ResourceFoundation\Proxy\EntityStructureProxy
ResourceFoundation\Proxy\EntityActivityProxy
ResourceFoundation\Proxy\EntityUserDataProxy
```

A project plugin can bind a proxy where a local implementation would normally be used.

Example:

```php
use Base3\Api\IContainer;
use ResourceFoundation\Api\IEntityAccessService;
use ResourceFoundation\Proxy\EntityAccessProxy;

$container->set(
	IEntityAccessService::class,
	fn($c) => new EntityAccessProxy(
		$c->get('microservicehelper')->get(
			'Xrm',
			IEntityAccessService::class,
			'entityaccessmicroservice'
		)
	),
	IContainer::SHARED
);
```

This allows the same consumer plugin to run against a local Memora backend or a remote XRM backend.

---

## Implementation Responsibility

ResourceFoundation does not register final implementations for resource services.
Implementation plugins and project plugins do that.

Example implementation mapping in Memora:

```text
IEntityDataService      -> MemoraEntityDataService
IEntityFileService      -> MemoraEntityFileService
IEntityProfileService   -> MemoraProfileService
IEntityAccessService    -> MemoraAccessService
IEntityRelationService  -> MemoraRelationService
IEntityMetadataService  -> MemoraMetadataService
IEntityTagService       -> MemoraTagService
IEntityStructureService -> MemoraStructureService
IEntityActivityService  -> MemoraActivityService
IEntityUserDataService  -> MemoraUserDataService
```

A project plugin may replace any of these with another implementation or a proxy.
Reusable plugins should not depend on the concrete Memora class unless they are explicit Memora extension plugins.

---

## Integration Examples

### Memora as XRM Backend

Memora implements ResourceFoundation contracts to access and manage XRM-style entities with support for typed data, names, tags, metadata, access, roles, profiles, and relations.

Example:

```php
$entry = $entityDataService->getEntry(123, [
	'loadtags' => true,
	'loadmetadata' => true,
	'loadaccess' => true
]);

$metadataService->setMetadata(123, [
	'external_id' => 'abc-123'
]);

$tagService->addEntryTags(123, ['important']);
```

### WebDAV / Nextcloud / File Storage

A WebDAV adapter can expose an `IFileStorage` implementation as a DAV endpoint.
The `IFileStorage` interface maps naturally to operations like PROPFIND, GET, PUT, DELETE, MKCOL, and stat-like metadata reads.

Example:

```php
$storage->write('/projects/demo/readme.txt', 'Hello world');
$content = $storage->read('/projects/demo/readme.txt');
```

### Microservice Deployment

A provider runtime can expose ResourceFoundation services as microservices.
A consumer runtime can bind ResourceFoundation proxies to those remote endpoints.

Provider-side:

```text
ResourceFoundation interface
  -> Base3XrmWebsite microservice class
    -> local Memora service
```

Consumer-side:

```text
consumer plugin
  -> ResourceFoundation proxy
    -> remote microservice connector
```

---

## Layer Integration

ResourceFoundation is designed to coexist with other BASE3 foundation areas:

| API | Purpose |
| --- | --- |
| `ResourceFoundation` | Entity data, files, query schemas, metadata, access, relations, profiles, user data |
| `AssistantFoundation` | AI providers, embeddings, vector search, tasks, agents |
| `MediaFoundation` | Images, audio, video, documents, vectors, rendering and editing abstractions |
| `CommunicationFoundation` | Explicit outbound publishing payloads, media attachments, delivery metadata |
| `MessagingFoundation` | Transactional and templated messages, queues, transports, templates, delivery tracking |
| `PublishingFoundation` | Simple discoverable publishing services |
| `UiFoundation` | UI/admin display contracts |

The foundation boundaries allow plugins to exchange stable DTOs and service interfaces without selecting concrete implementations too early.

---

## Extensibility

Developers can extend ResourceFoundation by implementing custom storage or data backends:

```text
DatabaseEntityService       SQL-based entities
MemoraEntityDataService     XRM-style entity storage
WebdavFileStorage           remote file storage
S3FileStorage               cloud object storage
InMemoryFileStorage         tests and temporary data
RemoteEntityProxy           microservice-backed entity access
CustomStructureService      project-specific type/module storage
```

When building a reusable plugin, depend on ResourceFoundation interfaces.
When building a project plugin, bind the final implementation in the container.

---

## Design Rules

* Keep interfaces implementation-neutral.
* Do not expose backend table names in ResourceFoundation interfaces.
* Use constructor injection for consuming services.
* Bind known service implementations in the container.
* Use proxies when the active implementation is remote.
* Keep final runtime choices in project plugins or custom bootstraps.
* Avoid direct dependencies from reusable feature plugins to Memora unless the plugin is explicitly a Memora extension.
* Keep DTOs simple and serializable when they need to cross microservice boundaries.

---

## Summary

ResourceFoundation unifies entity management, file storage, query schemas, resource profiles, roles and access, relations, metadata, tags, comments, structure definitions, user data, and microservice-ready proxies under one consistent set of contracts.

It is the foundation for content, document, knowledge, XRM, CRM, and automation features in the BASE3 ecosystem.
Concrete implementations such as Memora provide the actual storage behavior, while consumer plugins remain portable by depending on ResourceFoundation APIs.
