# SPEC2

## Overview

This document defines a proposed machine-to-machine interface for administrative remote inspection and control of a Tigermate CRM instance.

The design intentionally avoids changes to the existing webservice core.

Two distinct webservice operations are exposed:

- `inspect`
- `control`

They are meant to be consumed by the local client `vtc`.

## Goals

- Reuse the existing vtiger webservice infrastructure.
- Avoid core dispatcher changes.
- Keep read-only and mutating operations clearly separated.
- Make the protocol simple enough for a CLI client to consume.
- Restrict access to administrative users only.

## Non-Goals

- This document does not define server-side implementation details.
- This document does not define transport encryption or deployment topology.
- This document does not authorize unrestricted remote shell behavior.

## Endpoint

Both operations use the standard webservice entrypoint:

```text
/webservice.php
```

The operation is selected through the standard `operation` parameter.

## Authentication

Authentication follows the existing Tigermate/Vtiger webservice flow:

1. call `getchallenge`
2. call `login`
3. obtain `sessionName`
4. call `inspect` or `control` using that session

The client `vtc` must reuse the authenticated `sessionName`.

## Authorization

Both `inspect` and `control` are reserved to authenticated users with administrative privileges.

If the authenticated user is not an administrator, the server must reject the request.

Recommended error:

```json
{
  "success": false,
  "error": {
    "code": "ACCESS_DENIED",
    "message": "Administrative privileges are required"
  }
}
```

Actual error codes may follow the native webservice error model already used by Tigermate.

## Operation Split

### `inspect`

- HTTP semantic: read-only
- webservice type: `GET`
- purpose: inspection only
- allowed domains:
  - `database`
  - `filesystem`

### `control`

- HTTP semantic: mutating
- webservice type: `POST`
- purpose: amendments and changes
- allowed domains:
  - `database`
  - `filesystem`

## Common Request Model

Both operations share the same conceptual request model.

### Required parameters

- `operation`
- `sessionName`
- `command`
- `action`

### Optional parameters

- `path`
- `sql`
- `format`
- `payload`
- `options`

### Parameter meaning

- `command`: high-level target domain
  - `database`
  - `filesystem`
- `action`: specific operation inside the domain
- `payload`: structured data for complex requests, preferably encoded JSON
- `options`: optional structured execution flags

## Request Encoding

For simple calls, scalar parameters may be sent directly.

For more complex calls, the client should send structured data through `payload`.

Recommended convention:

- simple scalar values remain top-level parameters
- complex objects go into `payload`

Example conceptual payload:

```json
{
  "query": "SELECT * FROM vtiger_users LIMIT 10",
  "timeout": 5
}
```

## `inspect` API

## General Rules

- `inspect` must never perform writes.
- `inspect/database` must reject mutating SQL.
- `inspect/filesystem` must never modify files or directories.

## `inspect` Request Shape

```text
GET /webservice.php?operation=inspect&sessionName=...&command=...&action=...
```

## `inspect` Database Commands

### `command=database`

Supported actions should start with a narrow set.

### Recommended `database` actions

- `tables`
- `columns`
- `indexes`
- `query`
- `explain`

### `action=tables`

Purpose:

- list visible tables in the current CRM database

Recommended request:

```text
operation=inspect
command=database
action=tables
sessionName=...
```

Recommended result:

```json
{
  "success": true,
  "result": {
    "command": "database",
    "action": "tables",
    "items": [
      "vtiger_users",
      "vtiger_crmentity"
    ]
  }
}
```

### `action=columns`

Purpose:

- inspect column metadata for a table

Recommended parameters:

- `table`

### `action=indexes`

Purpose:

- inspect index metadata for a table

Recommended parameters:

- `table`

### `action=query`

Purpose:

- execute a read-only SQL query

Recommended parameters:

- `sql`

Allowed statements should be limited to read-only forms such as:

- `SELECT`
- `SHOW`
- `DESCRIBE`
- `EXPLAIN`

Rejected statements should include:

- `INSERT`
- `UPDATE`
- `DELETE`
- `REPLACE`
- `ALTER`
- `DROP`
- `TRUNCATE`
- `CREATE`
- `GRANT`
- `REVOKE`

Recommended result:

```json
{
  "success": true,
  "result": {
    "command": "database",
    "action": "query",
    "columns": ["id", "user_name"],
    "rows": [
      [1, "admin"]
    ],
    "rowCount": 1
  }
}
```

### `action=explain`

Purpose:

- return execution plan information for a read-only query

Recommended parameters:

- `sql`

## `inspect` Filesystem Commands

### `command=filesystem`

Supported actions should start with:

- `list`
- `read`
- `stat`

### `action=list`

Purpose:

- list files and directories under an allowed path

Recommended parameters:

- `path`

Recommended result:

```json
{
  "success": true,
  "result": {
    "command": "filesystem",
    "action": "list",
    "path": "modules/WSAPP",
    "items": [
      {
        "name": "WSAPP.php",
        "type": "file",
        "size": 1234
      }
    ]
  }
}
```

### `action=read`

Purpose:

- read a file from an allowed path

Recommended parameters:

- `path`

Optional parameters:

- `offset`
- `length`

### `action=stat`

Purpose:

- return metadata for a file or directory

Recommended parameters:

- `path`

## `control` API

## General Rules

- `control` is mutating by definition.
- `control` must still remain constrained and auditable.
- the first implementation should prefer explicit actions over unrestricted remote execution.

## `control` Request Shape

```text
POST /webservice.php
operation=control
sessionName=...
command=...
action=...
```

## `control` Database Commands

### `command=database`

Recommended actions:

- `execute`

### `action=execute`

Purpose:

- execute a mutating SQL statement

Recommended parameters:

- `sql`

Optional parameters:

- `transaction`

Recommended result:

```json
{
  "success": true,
  "result": {
    "command": "database",
    "action": "execute",
    "affectedRows": 3
  }
}
```

## `control` Filesystem Commands

### `command=filesystem`

Recommended actions:

- `write`
- `mkdir`
- `delete`
- `rename`

### `action=write`

Purpose:

- create or overwrite a file

Recommended parameters:

- `path`
- `content`

Optional parameters:

- `encoding`
- `createParents`

### `action=mkdir`

Purpose:

- create a directory

Recommended parameters:

- `path`

### `action=delete`

Purpose:

- delete a file or directory if allowed by policy

Recommended parameters:

- `path`
- `recursive`

### `action=rename`

Purpose:

- rename or move a file inside allowed roots

Recommended parameters:

- `path`
- `target`

## Response Model

The response should follow the standard webservice shape already used by Tigermate:

### Success

```json
{
  "success": true,
  "result": {}
}
```

### Error

```json
{
  "success": false,
  "error": {
    "code": "...",
    "message": "..."
  }
}
```

## Error Categories

Recommended categories for the client to handle:

- authentication failure
- authorization failure
- invalid command
- invalid action
- invalid path
- invalid SQL
- forbidden operation
- target not found
- execution failure

## Client Behavior for `vtc`

The `vtc` client should:

1. authenticate using the standard webservice login flow
2. store and reuse `sessionName`
3. call `inspect` for all read-only behaviors
4. call `control` for all mutating behaviors
5. surface server-side errors as first-class CLI failures
6. avoid assuming unrestricted access even for admin users

## Recommended Client Command Mapping

Suggested examples for `vtc` command design:

- `vtc inspect db tables`
- `vtc inspect db columns vtiger_users`
- `vtc inspect db query "SELECT * FROM vtiger_users LIMIT 10"`
- `vtc inspect fs list modules/WSAPP`
- `vtc inspect fs read modules/WSAPP/WSAPP.php`
- `vtc control db execute "UPDATE ..."`
- `vtc control fs write path/to/file --content "..."`

The exact CLI syntax is up to the client project, but this mapping is the intended semantic model.

## Security Constraints

The server implementation should enforce at least the following:

- admin-only access
- path allowlists for filesystem operations
- path canonicalization before access
- rejection of traversal attempts
- rejection of mutating SQL under `inspect`
- audit logging for `control`
- bounded result sizes for large reads and queries

## Stability Guidance

To keep the protocol stable for `vtc`, the following should remain stable over time:

- operation names: `inspect`, `control`
- command names: `database`, `filesystem`
- top-level response shape: `success`, `result`, `error`

New actions may be added later without breaking old clients.

## Minimal First Version

A practical first version should support only:

### `inspect`

- `database/tables`
- `database/columns`
- `database/query`
- `filesystem/list`
- `filesystem/read`
- `filesystem/stat`

### `control`

- `database/execute`
- `filesystem/write`
- `filesystem/mkdir`

Everything else can be added later.

## Final Recommendation

For the first implementation, `vtc` should assume:

- `inspect` is safe read-only administration
- `control` is privileged mutating administration
- both are authenticated webservice operations
- both may reject requests outside server policy even for admin users
