Atarashii MAL API
=================

This software is an unofficial API for the MyAnimeList website built on the
Symfony PHP framework.

Due to a poor official API that offers spotty coverage, the need exists for
something that offers more features in a REST-ful manner.

The API "1.0" version tries, where possible, to offer the same interface as
provided by the [unofficial MyAnimeList API](https://github.com/chuyeow/myanimelist-api).
Please review COMPATIBILITY for a list of differences.

Download
--------

Bitbucket: https://bitbucket.org/ratan12/atarashii-api

Installation and Configuration
------------------------------

Note: If you want to try out the API, or develop against a fully working
environment, a configuration for Vagrant is provided. Please see VAGRANT for
details.

You will need [Composer](https://getcomposer.org/) to set up the project.

Additionally, you will need to get a user agent whitelisted with MyAnimeList.
See [this forum topic](http://myanimelist.net/forum/?topicid=682709) for
information on what to do.

Once downloaded, run `composer install` in the root directory to install the
Symfony components and configure the core application parameters.

You can either run the application on PHP's built-in web server or configure it
as an application on a full web server.

For the built-in server, run `php app/console server:run`. If you configure on a
web server, set the document root as the web directory.

Contributing
------------

Please see CONTRIBUTING for details.

Credits
-------

This code was inspired by the Unofficial MAL API by Chu Yeow.

It makes use of the following major components:

* [Symfony PHP framework](http://symfony.com/)
* [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle) by Friends of Symfony
* [JMSSerializerBundle](http://jmsyst.com/bundles/JMSSerializerBundle) by Johannes Schmitt

This project is not sponsored by, endorsed by, or affiliated with MyAnimeList, a
property of MyAnimeList, LLC.

License
-------

The Atarashii MAL API is Copyright © 2015 Ratan Dhawtal and Michael Johnson. It
is licensed under the Apache Public License 2.0.
