---
layout: docs
title: Utilities
permalink: /docs/utilities/
---

Strata ships with a set of tools that aim to ease development.


## Hash and Inflector

It borrows the awesome [`Inflector`](http://book.cakephp.org/2.0/en/core-utility-libraries/inflector.html), [`Hash`](http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html) and its requirement [`String`](http://book.cakephp.org/2.0/en/core-utility-libraries/string.html) classes from CakePHP 2.

These 3 objects are packaged under `Strata\Utility\` and can be loaded as expected. For example, to gain access to Hash you can add `use Strata\Utility\Hash;` to the top of your class definition.

## debug();

Strata declares a global function named `debug()`. This function is useful while developing because it allows you to dump object values both in the HTML and the logs. If you had previously declared your own global function named `debug`, we will not inject our version of the function.

The function accepts anything as it's first and only parameter. It will print the value of the variable followed by a stack trace dump.

~~~ php
<?php
debug($myvar);
?>
~~~

