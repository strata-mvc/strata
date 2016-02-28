Strata
======

[![Build Status](https://travis-ci.org/francoisfaubert/strata.svg?branch=master)](https://travis-ci.org/francoisfaubert/strata) [![Join the chat at https://gitter.im/francoisfaubert/strata](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/francoisfaubert/strata?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Welcome

If you are (or wish to become) a Strata user please refer to the [official website](http://strata.francoisfaubert.com/) to obtain the latest information.

## Help and support

Feel free to ask questions to the Strata community in [the chat](https://gitter.im/francoisfaubert/strata) or raise any problems in the [issue page](https://github.com/francoisfaubert/strata/issues).

## Contributing

### Modifying the code

1. Fork and clone the repository.
1. Run `composer update`.
1. Run tests using `sh src/Scripts/test_strata`.
1. Update the docs using `sh src/Scripts/build_documentation`.
1. Fix your syntax using `vendor/bin/phpcbf --standard=PSR2 src`.

### Submitting Code

Code must be submit through pull requests. You need to follow the conventions you see used in the source already, which mostly consist of using the PSR2 standard.

1. Create a new branch, please don't work in your master branch directly.
1. Add new tests for the change you want to make.
1. Run the testing script to ensure your tests pass.
1. Push to your fork and submit a pull request (be clear about what you have done please).
