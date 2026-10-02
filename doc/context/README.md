# Context Format
This directory is a place to store contextual information about the tool.

Instead of WHAT has been decided, it is a place to store WHY or HOW waas that decision made, and was implemented to code.

The format will follow [MADR](https://github.toolset.workers.dev/adr/madr/blob/develop/template/adr-template.md), but not that strictly.

## Directories
There will be directories `product/`, `impl/`, `architecture/` and `discuss/`, to separate the docs' place depending on what it is about.

**`product/`** is for ideas and features regarding this tool, that does not necessarily cover the actual code. If you have an idea like, "I want to implement serialization!" or else, then add a document here. Filename would be `PROD-NNNN-title.md`

**`impl/`** is for the actual code. For example "Let's fix this coding style", then here. Filename would be `IMPL-NNNN-title.md`

**`architecture/`** is for architectural desicions. "We should separate these concerns to these different classes", would be an instance of a documentation here. Filename would be `ARCH-NNNN-title.md`

**`discuss/`** is for when you have an idea or concern, but don't have an concrete opinion about it yet, or just want to make sure about something unclear, then here it is. "I am not sure about this error handling, but I also don't have an answer. I am just concerned." would be an example here. Filename would be `RFD-NNNN-title.md`

Like the format rules, it is not strict, some topics overlap eachother, and may not be part of the four directories. `discuss/` is basiaclly for "general" decisions so put there if you are not sure. Once cleared, it will be properly supressed by a new doc somewhere inside the other three dirs.