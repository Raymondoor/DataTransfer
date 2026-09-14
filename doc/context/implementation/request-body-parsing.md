# Implementation note: RequestBody parsing

## Summary

`Frrame\Component\Http\RequestBody::load()` reads JSON and urlencoded request bodies from `php://input` directly, so they work on any HTTP method — not just POST.

## Context

PHP's SAPI only auto-populates `$_POST`/`$_FILES` for POST requests. That's a PHP limitation, not an HTTP one, but the original implementation trusted `$_POST`/`$_FILES` as if they were method-agnostic, so a `PUT`/`PATCH`/`DELETE` body was silently dropped.

## Approach

Dispatch in `load()` is driven by `Content-Type`, not by `RequestMethod`:

* `application/json` and (on non-POST) `application/x-www-form-urlencoded` are read from `php://input` and parsed manually — this works for any method.
* `multipart/form-data`, including file uploads, is still POST-only. An earlier version of this class hand-parsed multipart bodies for other methods too (writing uploaded parts to manual temp files), but that was reverted as more complexity than it was worth for this project — file uploads outside POST are simply unsupported for now.

## Gotchas

* `php://input` is a stream — read it once per request. `load()` already does this; don't call the parsing branches more than once per request.
* If multipart-on-non-POST support is ever revisited: manually-created temp files for uploaded parts are **not** real PHP uploads (`is_uploaded_file()`/`move_uploaded_file()` will refuse them), and PHP won't garbage-collect them the way it does real upload tmp files — cleanup has to be handled explicitly.
