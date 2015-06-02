---
layout: docs
title: Testing
permalink: /docs/shell/testing/
---

Strata uses [PHPUnit](https://phpunit.de/) as test suite for Test Driven Development. All test cases must be located under the `test` directory and end with the `Test` keyword.

Each time you generate a file using the [generator](/docs/generator/) a corresponding test file is created. It is your duty to update it with assertions so your application's files are tested.

To run all the  test, run `bin/strata test`. A PHPUnit output will describe the details of the tests.

One could add this command to a `grunt watch` script so that your application is always tested as you add to or modify your project files.

## Fixtures

{% include workinprogress.html %}

There is no inforced way of creating fixtures for your test currently.

## Wordpress fixture

{% include workinprogress.html %}

There are currently plans to load up Wordpress as a callable object during the execution of the test suite. It needs to be disconnected from MySQL to remain quick, but one will be able to test assumptions using native Wordpress commands.
