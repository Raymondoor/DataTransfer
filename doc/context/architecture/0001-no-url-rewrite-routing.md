---
status: accepted
date: 2026-08-08
---

# Routing is file-path-based; no URL-rewrite requirement

## Context and Problem Statement

Most PHP frameworks route every request through a single front controller, reached via a `mod_rewrite`/`.htaccess` rule (or equivalent nginx `try_files`) that maps arbitrary URLs onto that one file. That requires webserver-config access the target hosting environment (cheap/shared PHP hosting, environments where config can't be changed) doesn't reliably provide. How should this project handle routing without depending on rewrite rules being available?

## Decision Drivers

* Must work when uploaded via plain FTP to hosting with no `.htaccess`/`mod_rewrite` support and no control over the docroot.
* Should stay understandable without a routing table or annotation scanning.

## Considered Options

* Front controller + `mod_rewrite`/`.htaccess` (the conventional approach)
* File-path-based routing: an `index.php`'s path relative to the docroot *is* the route

## Decision Outcome

Chosen option: "File-path-based routing" — `public/index.php` handles `/`, `public/foo/index.php` handles `/foo`, and so on, with no rewrite rule involved. See `agent/structure/implementation.md`'s Routing section for the full rule, including that the docroot itself doesn't have to be `public/` either.

### Consequences

* Good, because the project runs unmodified on hosting with zero rewrite config or docroot control.
* Good, because "what handles this URL" is answerable by looking at the filesystem, no routing table to cross-reference.
* Bad, because there's no central place to see every route at once, and no built-in support for path parameters (`/user/{id}`) — those need to be handled inside the entry point itself (e.g. via `$_SERVER['PATH_INFO']`) if a project needs them.
