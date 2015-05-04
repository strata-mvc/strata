---
layout: docs
title: Testing your Strata app
permalink: /docs/testing/
---

Strata uses [PHPUnit](https://phpunit.de/) as test suite for Test Driven Development. All test cases must be located under the `test` directory and end with the `Test` keyword.

Each time you generate a file using the [generator](/docs/generator/) a corresponding test file is created. It is your duty to update it with assertions so your application's files are tested.

To run all the  test, run `bin/strata test`. A PHPUnit output will describe the details of the tests.

One could add this command to a `grunt watch` script so that your application is always tested as you add to or modify your project files.
