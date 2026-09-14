# Frrame — Current Implementation

This document describes how the current repository uses the Frrame structure.

It is not a specification of Frrame. It records the concrete choices made in this repository so that an agent working on the repository can understand existing code without treating every choice as a requirement.

A different application may organize or implement the same concepts differently.

## Top-level structure

```text
app/        PHP application code
resource/   application resources
script/     CLI and maintenance scripts
doc/        project documentation
log/        runtime logs
test/       tests
public/     current web document root
```

`public/` is used by this repository as the web document root. It is not a requirement for every Frrame application.

## `app/`

The current application uses the following subdirectories:

```text
app/
├── Base/
├── Component/
├── Controller/
├── Dictionary/
├── Facade/
├── Factory/
├── Logic/
├── Model/
├── Util/
└── View/
```

These represent the roles currently used by this repository.

### `Base/`

Base classes from which concrete application classes may inherit.

For example:

* `Base/Controller.php`
* `Base/Model.php`

These are conveniences of the current implementation rather than framework contracts.

### `Controller/`

Classes responsible for individual application entry-point behavior.

The current repository contains `WelcomeController` as a small working example.

### `Model/`

Concrete classes representing application data.

The current repository contains `LogsModel` as an example.

### `Dictionary/`

Fixed sets of values.

This is useful for values that have a known, limited set of meanings, including values that would naturally be represented by PHP enums.

A project may instead use native enums, constants, or another representation where appropriate.

### `Component/`

Independent reusable application components.

The current repository contains components for concerns such as HTTP input, sessions, database access, internationalization, logging, and exception handling.

Components are not required to follow the exact set used here.

### `Facade/`

Classes used to compose multiple application components for a particular purpose.

For example, the current `MiddlewareFacade` groups the bootstrap operations used by web entry points.

This is a composition choice of this repository, not a requirement to implement middleware or facades in a particular way.

### `Factory/`

Construction helpers.

The directory currently contains no substantial implementation.

### `Logic/`

Application/domain processes that do not naturally belong to a more specific role.

The directory currently contains no substantial implementation.

### `Util/`

Small self-contained utilities.

The current repository contains `Str` for string-related helpers.

### `View/`

Code concerned with producing application output.

The current repository contains the view presentation and page-context utilities used by its example application.

## `resource/`

The current repository uses:

```text
resource/
├── asset/
├── data/
├── i18n/
├── migration/
└── view/
```

These contain frontend source, application data, translations, database migration definitions, and views respectively.

Their exact organization is specific to this repository.

## `script/`

Contains executable CLI scripts.

The current repository uses this for database migration and seeding scripts.

These scripts are intended to be invoked explicitly rather than through web requests.

## `public/`

This repository currently uses `public/` as its document root.

Each entry point is responsible for handling its own request and producing its output. There is no requirement in this repository for a separate router or URL-rewriting layer.

For example:

```text
public/index.php
```

is the entry point for `/`, while:

```text
public/api/index.php
```

is an entry point for `/api`.

This is the current implementation's routing arrangement.

## Dependencies and tooling

This repository currently uses Composer for PHP dependencies and Vite for frontend development and production builds.

These are development choices of this repository and are not part of the conceptual requirements of Frrame.

## Working rule

When extending this repository, follow the existing implementation where it is useful and appropriate.

Do not introduce a conventional framework structure merely because it is conventional.

At the same time, do not treat an existing directory, class, or implementation detail as immutable. The application may evolve when its requirements call for it.
