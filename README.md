# ResourceFoundation

**ResourceFoundation** is a core component of the BASE3 framework that provides a unified, extensible data access layer for all types of system resources. It acts as a bridge between different kinds of resource backends such as XRM entities, file systems, and relational data sources.

---

## Overview

ResourceFoundation defines common interfaces and base classes for loading, saving, and managing resources, whether they are database entries, structured entities, or file-based objects. It enables other BASE3 plugins, like Cognora or MissionBay, to work with data in a consistent and abstracted way.

### Core Concepts

* **Entities**: Logical data objects (e.g., users, projects, documents) accessible through `IEntityDataService`.
* **Files**: Physical or virtual files accessed via `IFileStorage` (local, WebDAV, Nextcloud, or S3).
* **Relations**: Structured links between entities (alloc, tree, link), managed transparently by implementations.
* **Lazy Loading**: Load only relevant parts of a resource (`loadtags`, `loadallocs`, etc.) on demand.

---

## Key Interfaces

### `IEntityDataService`

A generic CRUD interface for data resources.

```php
public function getEntries(array $options = []): array;
public function getEntry(int|string $id, array $options = []): ?array;
public function saveEntry(array $data): int|string;
public function deleteEntry(int|string $id): bool;
```

This interface is the foundation for more advanced services such as the XRM resource layer (`IXrmService`).

### `IFileStorage`

Provides generic file access independent of storage backend:

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

---

## Integration Examples

### Cognora (XRM Backend)

Cognora implements `IEntityDataService` to access and manage XRM-style entities with support for relationships (`alloc`, `tree`, `link`) and metadata (`tags`, `rating`, `access`).

```php
$entry = $xrm->getEntry(123, ["loadtags" => true]);
$entry['tags'][] = 'important';
$xrm->saveEntry($entry);
```

### WebDAV / Nextcloud

A WebDAV adapter can expose any ResourceFoundation-compatible service as a DAV endpoint. The `IFileStorage` interface maps directly to WebDAV operations like PROPFIND, GET, PUT, and DELETE.

```php
$storage->write('/projects/demo/readme.txt', 'Hello world');
$content = $storage->read('/projects/demo/readme.txt');
```

This allows BASE3 systems to integrate seamlessly with external file services like Nextcloud or ILIAS.

---

## Layer Integration

ResourceFoundation is designed to coexist with other major BASE3 APIs:

| API                     | Purpose                                                 |
| ----------------------- | ------------------------------------------------------- |
| **AssistentFoundation** | AI and automation features (e.g., MissionBay, OpenAI)   |
| **ReportFoundation**    | Reporting, querying, and visualization (DataHawk)       |
| **ResourceFoundation**  | Core data and object management (XRM, files, relations) |

Each API is independent but interoperable. For example, ReportFoundation can query data provided by ResourceFoundation, and AssistentFoundation can modify or enrich entities stored via ResourceFoundation.

---

## Extensibility

Developers can extend ResourceFoundation by implementing custom storage or data backends:

* `DatabaseEntityService` for SQL-based entities
* `WebdavFileStorage` for remote files
* `S3FileStorage` for cloud storage
* `InMemoryStorage` for testing environments

---

## Summary

ResourceFoundation unifies entity management, file storage, and data relations under one consistent interface. It is the foundation for content, document, and knowledge management in the BASE3 ecosystem, enabling integrations across Cognora, MissionBay, and future extensions.

