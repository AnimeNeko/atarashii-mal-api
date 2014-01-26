Atarashii MAL API
=================

This software is an unofficial API for the MyAnimeList website built on the
Symfony PHP framework.

Due to a poor official API that offers spotty coverage, the need exists for
something that offers more features in a REST-ful manner.

Where possible, this code tries to offer the same interface as provided by the
[unofficial MyAnimeList API](https://github.com/chuyeow/myanimelist-api). Please
review [COMPATIBILITY](COMPATIBILITY.md) for a list of differences.

Download
--------

Bitbucket: https://bitbucket.org/ratan12/atarashii-api

Installation and Configuration
------------------------------

You will need [Composer](https://getcomposer.org/) to set up the project.

Once downloaded, rename app/config/parameters.yml.dist to parameters.yml and run
`composer install` in the root directory to install the Symfony components.

You can either run the application on PHP's built-in web server or configure it
as an application on a full web server.

For the built-in server, run `php app/console server:run`. If you configure on a
web server, set the document root as the web directory.

Contributing
------------

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

Credits
-------

This code was inspired by the Unofficial MAL API by Chu Yeow.

It makes use of the following major components:
* [Symfony PHP framework](http://symfony.com/)
* Friends of Symfony's [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle)
* Johannes Schmitt's [JMSSerializerBundle](http://jmsyst.com/bundles/JMSSerializerBundle)

This project is not sponsored by, endorsed by, or affiliated with MyAnimeList, a
property of CraveOnline, LLC.

License
-------

The Atarashii MAL API is Copyright © 2014 Ratan Dhawtal and Michael Johnson. It
is licensed under the [Apache Public License 2.0](LICENSE).
