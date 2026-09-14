# app/Logic

Business/domain process classes — e.g. an `Auth` flow that checks credentials, touches `Session`, and decides what happens next. For orchestration that doesn't belong to a single Model or Controller, not for thin single-concern wrappers (those are `app/Component`).
