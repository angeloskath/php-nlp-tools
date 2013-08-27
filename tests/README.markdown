Testing information
===================

This readme contains a bit of information regarding writing tests for NlpTools and executing them.

Writing Tests
-------------

* Test classes should be in the same namespace as the class that is being tested
* Any data needed for the test or produced by the test should be in the 'data' directory
  under the same folder as the namespace. Only data needed (not produced) are commited to
  the repository.
* Tests should be marked with the groups **Slow** and **VerySlow** if they require more than
  10 seconds and 1 minute respectively. If a test is marked as VerySlow it should also be marked
  as Slow.
* Both functional and unit tests are welcome.

Executing Tests
---------------

Currently only one testsuite is defined (all tests). Because some tests take a long time to
run you can try running `phpunit --exclude-group Slow` or `phpunit --exclude-group VerySlow`
to avoid some slow tests.

PHPUnit should be run from inside the tests folder or the phpunit.xml file should be provided
as config.
