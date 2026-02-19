# AGENTS.md

This file provides guidance to AI agents when working with code in this repository.

**Always update this file automatically when you learn new things about the code base!**

**Never assume existence of functions or variables. If you haven't found them in code, but you assume they exist, always ask for confirmation.**

## Project Overview

This is the @@PLUGIN_NAME@@ DokuWiki plugin.

It's for @@PLUGIN_DESC@@

**FIXME** Replace this section with a more detailed description of the plugin's purpose and specific file structure (e.g., frontend assets, script locations, and main classes) when you first inspect or extend the codebase.

## Testing

Tests run via DokuWiki's PHPUnit-based testing framework:

```bash
# Tests must be run from repository root
../../../bin/plugin.php dev test

# run individual test file
../../../bin/plugin.php dev test _test/GeneralTest.php

# create a new test file
../../../bin/plugin.php dev addTest MyClass
```

DokuWiki provides useful helper methods for testing:

* `DokuWikiTest::getInaccessibleProperty()` to access private/protected properties
* `DokuWikiTest::callInaccessibleMethod` to execute private/protected methods
* read `../../../_test/core/DokuWikiTest.php` for more helper methods
* use `../../../_test/TestRequest.php` to simulate HTTP requests for integration tests
* use `../../../_test/phpQuery-onefile.php` if you need to parse HTML in tests

Each test run will provide a fresh DokuWiki instance in a temporary directory via the default setupBeforeClass methods.

## Linting, Formatting and Conventions

### PHP

Adhere to PSR-12 coding standards. Always add proper docblocks with descriptions, parameter types, and return types to all classes, methods and functions.

```bash
# Lint PHP files using PHP_CodeSniffer (must be run from repo root)
../../../bin/plugin.php dev check

# Auto-Fix formatting issues using PHP_CBF and Rector (must be run from repo root)
../../../bin/plugin.php dev fix
```

### JavaScript

Frontend JavaScript targets modern browsers (Chrome, Safari, Firefox, Edge). Use ES2015+ features such as `const`/`let` and classes where appropriate.
Add JSDoc and other comments for non-trivial code.

**FIXME** Define jQuery usage policy for this specific plugin.

### CSS / Styles

Both `.css` and `.less` files are supported and loaded.
Create style files to target specific modes:

* **all** - `all.<EXT>` for all viewing modes
* **screen** - `style.<EXT>` or `screen.<EXT>`
* **print** - `print.<EXT>` for print styles

**Naming:** Prefix class names and IDs with `plugin__@@PLUGIN_NAME@@`.

**Utility:** Use DokuWiki's global `.hidden` class for visibility toggles.


## Plugin Architecture

Inspect the base plugin classes in `../../../inc/Extension/` to learn about the plugin system architecture.

```bash
# add new plugin components (must be run from repo root)
../../../bin/plugin.php dev addComponent <type>
# e.g.
../../../bin/plugin.php dev addComponent action
# if multiple of the same type are needed, give a name:
../../../bin/plugin.php dev addComponent action foobar
# -> creates action/foobar.php
```

Additional classes are autoloaded when using the `dokuwiki\plugin\@@PLUGIN_NAME@@` namespace.

### Localization

Text output to users should not be hardcoded, but stored as language strings in the `lang/` directory of the plugin.

If not present yet, initialize via dev plugin:

```bash
# add new English default files (must be run from repo root)
../../../bin/plugin.php dev addLang
```

#### JavaScript Localization

Put all localization strings for use in JavaScript into the `lang/` directory with the `['js']` array key:

```php
$lang['js']['identifier'] = 'Content';
```

In JavaScript files, fetch them from the global object: `LANG.plugins.@@PLUGIN_NAME@@.<identifier>`.

### Plugin Configuration

Put all plugin configuration options in the `conf/` directory. If it doesn't exist, initialize with the dev plugin:

```bash
# add config files (must be run from repo root)
../../../bin/plugin.php dev addConf
```
