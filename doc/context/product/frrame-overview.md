# Product context: Frrame

## Problem

Most PHP frameworks force a project into mandatory machinery (front controller, router owning the URL space, a request/session wrapper every layer is coupled to) before a single feature is written — and much of that machinery assumes hosting control (`mod_rewrite`, a configurable docroot) that cheap/shared PHP hosting doesn't reliably give you.

## Who this is for

Developers who want a PHP project to start from a small, readable directory convention instead of a framework they have to learn, work around, or fight — and who may be deploying to hosting they don't fully control.

## Goals

* Work when copied via plain FTP onto hosting with no rewrite rules and no docroot control.
* Keep every "core" concern (sessions, HTTP headers, DB access, …) as an optional, independent, swappable class the project owns outright — not a vendor package.
* Stay understandable by reading the directory structure, without a manual.

## Non-goals

* Not a full-featured framework — no bundled ORM, DI container, or router. Nothing stops a project from adding one; Frrame just doesn't ship one by default. See `agent/structure/framework.md`'s "Why this exists" section.
* Not opinionated about the tech stack beyond the PHP directory conventions themselves (dotenv/Monolog/Vite/Alpine/htmx in this repo are this project's own picks, not requirements).

## Current state

Frrame itself is still a blueprint (see `agent/structure/framework.md` and `agent/structure/implementation.md`) — this repo has one working example route, not a shipped product feature set.
