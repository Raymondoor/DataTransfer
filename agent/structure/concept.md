# Frrame — Concept

Frrame is a way of conceptualizing an application and expressing that perspective through its structure.

It is not intended to prescribe how an application must be implemented. The structure provides places and roles from which an application can be developed, while the application remains free to interpret and extend them.

The structure is therefore a starting point, not a complete specification of every application built with Frrame.

## Scope

Frrame is concerned primarily with how an application can be understood and organized.

It does not require a particular:

* framework or runtime
* routing system
* database system
* dependency-injection system
* templating system
* frontend framework
* application domain
* programming style

A project may use these things where they are useful.

Likewise, a project may introduce structures that Frrame does not describe. Frrame does not attempt to account for every possible concern an application may have.

## Structure

The top-level structure provides broad places for different kinds of application material.

* `app/` — application code
* `resource/` — application resources that are not PHP application code
* `script/` — executable development or maintenance scripts
* `doc/` — project documentation
* `log/` — runtime-generated logs

These are conventions rather than a complete set of requirements. A project may not need every directory, and additional directories may be appropriate when the application requires them.

The important part is the distinction between the roles represented by the structure, rather than the exact set of names.

## Application code

`app/` contains the PHP code belonging to the application.

Its internal structure is intentionally open.

Frrame does not require an application to use a particular collection of classes such as controllers, models, services, repositories, or components. Those structures may be useful for a particular application, but they are implementation decisions.

The same applies to the names and organization of subdirectories.

## Resources

`resource/` contains material used by the application but which is not itself PHP application code.

Examples may include:

* views
* translations
* frontend source
* database definitions
* other application data

The exact organization depends on the application.

## Web applications

Frrame is suitable for web applications, but the concepts described here are not intended to define a web framework.

A web application may use `public/` as a document root, or organize its entry points differently. Routing, URL rewriting, request dispatch, and similar mechanisms are implementation concerns rather than the purpose of Frrame itself.

## In short

Frrame provides a way to start organizing an application.

It deliberately leaves room for the application to become its own thing.
