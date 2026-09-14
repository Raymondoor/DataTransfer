# Frrame Structure

This document describes the structure currently used by the Frrame repository.

It describes the repository as it exists today, rather than defining requirements for every Frrame application. An application using Frrame may have a different structure where appropriate.

## Root Structure

```text
.
├── app/
├── resource/
├── script/
├── doc/
├── log/
├── public/
├── test/
├── agent/
├── composer.json
├── package.json
└── ...
```

### `app/`

Contains the application's PHP code.

The directory is organized by the role of the code rather than by technical layer alone. The current implementation contains areas such as:

```text
app/
├── Base/
├── Component/
├── Controller/
├── Dictionary/
├── Facade/
├── Logic/
├── Model/
└── View/
```

These directories represent the roles used by the current implementation. They are not a requirement that every application must reproduce exactly.

### `resource/`

Contains resources used by the application.

This includes resources that are processed, bundled, or otherwise consumed by the application rather than being directly served as public files.

### `script/`

Contains executable scripts used by the application or its development environment.

Scripts here are intended to perform operations outside the main application request flow.

### `doc/`

Contains documentation belonging to the application or project.

This is intended for project-specific documentation such as architecture decisions, RFCs, development notes, and other information useful to the people working on the application.

Frrame's own structural documentation is kept outside this directory.

### `log/`

Contains runtime or application logs.

This directory is concerned with generated runtime information rather than source documentation.

### `public/`

The web-accessible document root used by the current implementation.

Files that need to be directly accessible by a web server are exposed through this directory.

The use of `public/` as the document root is a choice of this implementation, not a requirement of Frrame.

### `test/`

Contains automated tests for the application.

The current repository uses PHPUnit for PHP testing.

### `agent/`

Contains documentation intended for AI coding agents working with the repository.

It describes both the Frrame concept and implementation details that are useful when modifying the codebase.

This directory is not part of the application structure that developers are expected to reproduce in an application using Frrame.

## Application Structure

The current implementation organizes `app/` according to several recurring roles.

### `Base/`

Contains base classes used by other application classes.

These provide shared behavior where inheritance is appropriate.

### `Component/`

Contains reusable application components.

Components generally encapsulate functionality that can be used by multiple parts of the application without belonging to a particular page or business operation.

### `Controller/`

Contains controllers responsible for handling application-level requests and coordinating the appropriate response.

### `Dictionary/`

Contains fixed sets of application values.

These are useful for values that have a defined set of possible states or names.

### `Facade/`

Contains simplified interfaces to functionality that may otherwise require interacting with several underlying objects or services.

### `Logic/`

Contains application logic that does not naturally belong to a controller, model, or reusable component.

### `Model/`

Contains models representing and interacting with application data.

The current implementation uses models for persistence-related operations and representations of application data.

### `View/`

Contains the presentation side of the application.

Views are responsible for producing the output presented to the user.

## Resources

The internal structure of `resource/` follows the same principle as `app/`: its organization depends on what the application needs.

Resources may contain source files that are processed by development or build tools before being delivered to the application.

The current implementation uses Vite for frontend asset processing.

## Public Files

The distinction between `resource/` and `public/` is intentional.

`resource/` contains application resources and source material.

`public/` contains files that are exposed directly through the web server.

Not every resource therefore needs to have a corresponding public file.

## Development Files

Several files in the repository support development rather than the application itself.

* `composer.json` — PHP dependencies and Composer configuration
* `package.json` — frontend dependencies and npm scripts
* `phpunit.xml` — PHPUnit configuration
* `.env.sample` — example environment configuration

These describe the current implementation and its development environment.

## Current Implementation

The structure described here is the structure of the Frrame repository itself.

It should be read together with the Frrame concept rather than treated as a rigid project template. The repository provides one concrete example of how the structure can be used; applications are free to make changes when their requirements call for them.
