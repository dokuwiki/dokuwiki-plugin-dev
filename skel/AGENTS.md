# AGENTS.md

This file provides guidance to AI agents when working with code in this repository.

**Always update this file automatically when you learn new things about the code base!**

## Project Overview

This is the @@PLUGIN_NAME@@ DokuWiki plugin.

It's for @@PLUGIN_DESC@@

**FIXME** replace this section with a more detailled description when you first inspect or extend the codebase.

## Automated Testing

Tests run via DokuWiki's PHPUnit-based testing framework. The calls MUST be made from within the plugin's repository root using a relative path to the `bin/plugin.php` script!

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

**Important:** Test classes that need the plugin must set `protected $pluginsEnabled = ['@@PLUGIN_NAME@@'];` to enable it in the test environment.

**Important:** `setUp()` and `tearDown()` methods must be `public` (not `protected`) to match the `DokuWikiTest` base class.


## Caching

DokuWiki may cache JavaScript, CSS and rendered output. To reset the cache just touch the config file

```bash
touch ../../../conf/local.php
```

## Linting, Formatting and Conventions

Adhere to PSR-12 coding standards. Always add proper docblocks with descriptions, parameter types, and return types to all classes, methods and functions.

```bash
# Lint PHP files using PHP_CodeSniffer (must be run from repo root)
../../../bin/plugin.php dev check

# Auto-Fix formatting issues using PHP_CBF and Rector (must be run from repo root)
../../../bin/plugin.php dev fix
```

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
