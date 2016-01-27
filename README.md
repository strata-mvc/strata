Strata
======

[![Build Status](https://travis-ci.org/francoisfaubert/strata.svg?branch=master)](https://travis-ci.org/francoisfaubert/strata)

## Welcome

If you are (or wish to become) a Strata user please go to the [official website](http://strata.francoisfaubert.com/) to obtain the latest information.

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

All code must be submit through pull requests. Obviously, you need to follow the conventions you see used in the source already.

1. Create a new branch, please don't work in your master branch directly.
1. Add failing tests for the change you want to make. Run 'grunt watch' to see the tests fail.
1. Repeat until the tests do not fail and your feature/fix is complete.
1. Update the documentation to reflect any changes. Be clear about what you have done.
1. Push to your fork and submit a pull request.
