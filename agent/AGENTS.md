# Agent Instructions — Frrame

Frrame is a conceptual and structural starting point for applications. It is not a conventional framework whose implementation must be preserved as a fixed API.

Before making a structural change, read:

* `agent/structure/concept.md` — what Frrame is intended to represent.
* `agent/structure/implementation.md` — how this repository currently represents it.

## Important distinction

The concept and the current implementation are not the same thing.

`concept.md` describes the intended structure and scope of Frrame.

`implementation.md` describes the concrete choices currently made in this repository.

Do not turn an implementation detail into a framework requirement merely because it already exists here.

## Working on this repository

Prefer the existing structure when it fits the task.

Create new directories or classes when the application needs them. Do not add framework-like machinery merely because another PHP framework commonly provides it.

Similarly, do not remove an existing component merely because Frrame does not require that kind of component. It may be part of the current application's implementation.

When uncertain whether something belongs to Frrame itself or is simply an implementation choice, preserve the distinction rather than expanding the conceptual scope unnecessarily.

## Technology

The repository currently uses PHP, Composer, PHPUnit, and Vite, together with the dependencies recorded in its configuration files.

These technologies are part of the current implementation. They should not automatically be treated as requirements of Frrame.

## General principle

Keep the structure understandable.

Prefer a simple application-specific solution over introducing framework machinery whose purpose is only to make the repository resemble another framework.
