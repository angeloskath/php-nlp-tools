Contribution guidelines
===================

This document contains guidelines for contributing to NlpTools.

Coding style
------------------

NlpTools adheres to the [psr-2][1] standard. It also follows the convention of
appending the word *Interface* to any interface.

To enforce the psr-2 style it is suggested to use the [php-cs-fixer][2] tool.
While you 're at it why not enforce some more styles as well. The fixers used
are the **default** (which are more than the psr-2 level uses) but they will be
explicitly listed here in case they change in the future.

* indentation
* linefeed
* trailing_spaces
* unused_use
* phpdoc_params
* visibility
* return
* short_tag
* braces
* include
* php_closing_tag
* extra_empty_lines
* psr0
* control_spaces
* elseif
* eof_ending

The above fixers are the default.

Commenting Style
--------------------------

Every public method must have comments that follow the php doc convention.
@param and @return annotations are mandatory. The comments should be
explanatory not simply rewriting the method's name in a sentence. If the method
is too simple or the name explains the actions sufficiently then just add the
@param and @return annotations.

Examples of bad commenting currently in the develop branch:

``` php
/**
 * Calls internal functions to handle data processing
 * @param type $string
 */
public function tokenize($str)
{
    ......
}
```

It should be something along the lines of:

``` php
/**
 * Splits $str to smaller strings according to Penn Treebank tokenization rules.
 *
 * You can see the regexes in function initPatternAndReplacement()
 * @param  string $str The string to be tokenized
 * @return array  An array of smaller strings (the tokens)
 */
....
```

Equally necessary are class comments. The class comment should be explaining
what the class does from a high point of view. Redirections to online resources
like wikipedia are welcome. A good example that also contains a reference to an
external resource is the following:

``` php
/**
 * Implement a gradient descent algorithm that maximizes the conditional
 * log likelihood of the training data.
 *
 * See page 24 - 28 of http://nlp.stanford.edu/pubs/maxent-tutorial-slides.pdf
 * @see NlpTools\Models\Maxent
 */
class MaxentGradientDescent extends GradientDescentOptimizer implements MaxentOptimizerInterface
```

Pull Requests
--------------------

### Find something to work on ###

If it is your first contribution try to find something straightforward and
concise to implement without many design decisions as much as development
decisions. You could first submit an issue, if you like, and state your will to
correct this issue yourself.

### Branch off ###

When you 've found something to develop, create a new branch off of the develop
branch. Make your changes, add your tests (see below for testing) and then make
a pull request. Always keep your develop branch in sync with the remote and
before you create a pull request **rebase** your local branch to develop.

If you rebased but there has been a change pushed since, you don't have to
remove the pull request, rebase and recreate it. I will pull your changes
rebase them, merge them and then close the pull request. This will have the
effect of showing some merged pull requests as simply closed but it is worth
keeping the commit history clean.

So in two small sentences: Always create a new branch to develop on. Always
rebase before making a pull request.

### Tests ###

If you are implementing a new feature always include tests in your pull request.

Also contributing just tests is extremely welcome.

Testing
-----------

A bit of information can be found in the tests folder in the README file.

Tests should test the implementation thoroughly. You can test your
implementation like a black box, based only on the outputs given some inputs,
or you can test every small part for how it works. Either is acceptable. I will
make my point clear with an example.

The PorterStemmer implementation has 5 steps and some even have sub steps. One
way to write the test would be to expose those steps (maybe by extending the
PorterStemmer class) and write tests for each one. One other way would be to
simply take a big list of English words and their stems according to the
canonical implementation and check if your code produces the same results.

While the second is a lot easier to implement, in case of failure, it gives
very little information regarding the cause of the error. Both are acceptable
(in the case of the example the second is implemented).

[1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[2]: http://cs.sensiolabs.org/
