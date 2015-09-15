<?php

namespace Atarashii\APIBundle\Tests\Util;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class LoginCredentials
 * @package Atarashii\APIBundle\Util\LoginCredentials
 */
class LoginCredentials
{
    public static function get(Container $container)
    {
        $credentials = $container->getParameter('unit_testing');

        if ($credentials['username'] === 'CHANGEME') {
            return false;
        } else {
            return $credentials;
        }
    }

}