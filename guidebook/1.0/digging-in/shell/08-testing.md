---
layout: guidebook
title: Testing
permalink: /guidebook/1.0/digging-in/shell/testing/
menu_group: shell
---

Strata uses [PHPUnit](https://phpunit.de/) and [Behat](http://behat.org) as test suites for application testing. All test cases must be located under the `test` directory and end with the `Test` keyword.

Each time you generate a class using the [generator](/docs/generator/) a corresponding test file is created. It is your duty to update it with assertions so your application's files are fully tested.

To run the test suite, run `./strata test`. A PHPUnit output will describe the details of the tests.


## TDD

There is a bash script that will automate the validation of your changes made available through a [Github Gist](https://gist.github.com/francoisfaubert/65586e22d47c690ad88e3bc923e80a1d) that you may find useful.

Download a copy of the script under ~/bin/tdd and give it `execute` rights. Afterwards you may invoke the bash script before starting your work session. The script will switch between Gherkin or unit tests depending on the file that has triggered the changes.
