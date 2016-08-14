Installation and Configuration
==============================

*Note:* If you want to try out the API or develop against a fully configured
setup, you can use a Vagrant machine. Please see VAGRANT for details.

Before installing, make sure to check the REQUIREMENTS document for a list of
what is needed to run this software.

Gathering the Dependencies
--------------------------

To reduce download size and make development easier, this software does not
include many of the library dependencies that it uses. It, instead, relies on a
tool called [Composer](https://getcomposer.org/) to help download and manage
these dependencies and do the basic configuration of the software.

Install Composer using the directions on the website. Once it is installed,
you can run `composer install` or the equivalent in the root of the application
directory to download the needed Symfony and other third-party components.
Additionally, you will be asked for some information for the basic software
configuration. Most of the defaults are safe, so you can accept them if you
don't know what to use.

Running Locally Directly With PHP
---------------------------------

Symfony includes the ability to run directly from PHP using a built-in web
server. This is a great tool for simple testing and review of the software and
this software supports that capability.

For the built-in server, run `php app/console server:run`. The output will show
where to browse and access the software.

For more information on this method, read [How to Use PHP's built-in Web Server](
https://symfony.com/doc/2.8/setup/built_in_web_server.html)

Deploying On a Web Server
-------------------------

This software is designed to be deployed as a general Symfony Web Application.

Instructions for deploying with Apache HTTPd or nginx can be found in the
[Symfony Documentation](https://symfony.com/doc/2.8/setup/web_server_configuration.html).
For other platforms such as Azure Website Cloud or Heroku, look at the [Symfony
Deployment Guides](https://symfony.com/doc/2.8/deployment.html).
