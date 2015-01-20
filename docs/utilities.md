---
layout: docs
title: Utilities
permalink: /docs/utilities/
---

Wordspress MVC ships with a tools that aim to ease development.

It borrows the awesome [`Inflector`](http://book.cakephp.org/2.0/en/core-utility-libraries/inflector.html), [`Hash`](http://book.cakephp.org/2.0/en/core-utility-libraries/hash.html) and its requirement [`String`](http://book.cakephp.org/2.0/en/core-utility-libraries/string.html) objects from Cakephp 2.

These 3 objects are packaged under `MVC\Utility\` and can be loaded as expected. For exemple, to gain access to Hash you can add `use MVC\Utility\Hash;` to your class.

Additionnally, we have a list of internal utilies tailored for use within wordpress.

* [debug()]({{ site.baseurl }}/docs/utilities/debug/)
* [EmailLoader]({{ site.baseurl }}/docs/utilities/emailloader/)
