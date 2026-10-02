# Decide General Concept
---
status: accepted
date: 2026-10-02
decision-makers: <torhc17311@gmail.com>
---

## Context and Problem Statement
I need a way to transfer data between two databases, but with different schemas.
Creating raw sql for every record was fine for same/similar schema data, but not for data with big differences.

For example, you may have a `users` table `[id, name, email, group]`. But if you decided to refactor this and make it into two tables `users` and `groups`, extracting `group` column would take some processing. It would be easier if I can just declare how it's done inside a PHP file.

For the above to be achieved, the following requirements can be true.
### Requirements
1. Has to have the capability to transfer data as is to another database.
2. Record of how the tranasfer is/was done should be inspectable before and after the transfer.
3. User of this tool has to be able to extend the capability of this tool without needing to touch the "core".
4. The entire transfer process has to be easy to understand, on the basis that the user knows basic SQL operations.
5. To achieve (4), whatever change on schema has to be limited to something simple, multiple conditions are not allowed.
6. To achieve (4), each change state has to be recorded so to be usable later.
7. To achieve (2, 6), the recorded change has to be "immutable".
8. To achieve (1), should support multiple types of Database drivers. At least the ones supported by PDO.

## Decision Drivers
- Not wanting to go through long process, for moving data to a newer version of software.
- Not wanting to record the migration process to an excel file every single time.

## Considered Options
- Make a new tool usable with the same language as the webapp, here php
- Keep it as it is, manually inserting sql

## Decision Outcome
Chosen option: "Make a new tool usable with the same language as the webapp, here php", because this was the only option.