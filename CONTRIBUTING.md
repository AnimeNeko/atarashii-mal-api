How to Contribute
=================

Participation of the wider community is essential to keep this code evolving. We
simply can't think up every scenario that someone using the API may want to use.

To make it easy to contribute your ideas to this software, we have a few
guidelines to follow. It'll make it easier to review your code, easier for
others to understand your code, and make the project feel more cohesive.

Getting Started
---------------

* Make sure you have a [Bitbucket](https://bitbucket.org/) account.
* Submit a ticket for your problem, if one doesn't exist.
  * Make sure to put in all the important details, including version of the API.

Making Changes
--------------

Want to make a fix or add a new feature? We accept pull requests. Follow the
directions below to make the process easy.

* Make a fork of the project on Bitbucket.
* Make a new branch to store your changes.
  * This should usually be branched from "master".
  * Only branch off something different if you are certain the changes must be
    on that branch.
* Make each commit a logical unit of changes. If you like to commit often, use
  a rebase to squash commits.
* Check that you are following the coding conventions of the project before
  submitting.
* Make sure you don't have unnecessary whitespace changes by using
  `git diff --check` before committing.
* Ensure your commit messages are in the proper format:

````
    Short Description of Change

    This should be a longer description of the changes made, including all
    relevant details so someone looking only at the log has a good idea of
    what was done. Each line should be no more than 72 characters long, so
    hard-wrap long lines. Both the short and long description should be in
    the imperative voice and active - "updates" rather than "updated".
````

Submitting Changes
------------------

* Push your changes to a topic branch in your forked repository.
* Create a pull request against the code.
* If this is a new feature, make sure to explain why it is needed and what it
  will be used for.

Coding Standards
----------------

This project tries to follow a common set of code standards so that everyone who
reviews or contributes will be easily able to read the existing code. Since we
are using Symfony as our framework, we try to follow the guidelines of the PHP
Framework Interop Group, which Symfony also follows. Specifically, this codebase
follows:

* [PSR-1](http://www.php-fig.org/psr/psr-1/)
* [PSR-2](http://www.php-fig.org/psr/psr-2/)

In cases where the above recommendations cannot be followed, documentation should
be made noting the reasoning.

Additional Resources
====================

* [Bitbucket 101](https://confluence.atlassian.com/display/BITBUCKET/Bitbucket+101)
* [General Bitbucket Documentation](https://confluence.atlassian.com/display/BITBUCKET/Bitbucket+Documentation+Home)
* [Working with Pull Requests](https://confluence.atlassian.com/display/BITBUCKET/Work+with+pull+requests)