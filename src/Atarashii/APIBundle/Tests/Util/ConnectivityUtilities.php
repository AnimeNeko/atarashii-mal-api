<?php

namespace Atarashii\APIBundle\Tests\Util;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class ConnectivityUtilities.
 */
class ConnectivityUtilities
{
    public static function getLoginCredentials(Container $container)
    {
        $credentials = $container->getParameter('unit_testing');

        if ($credentials['username'] === 'CHANGEME') {
            return false;
        } else {
            return $credentials;
        }
    }

    public static function checkConnection(Container $container)
    {
        $params = $container->getParameter('unit_testing');
        $doTest = $params['test_mal'];

        if ($doTest === false) {
            return array(false, 'Live tests for MyAnimeList are disabled by configuration.');
        }

        $socket = @fsockopen('www.google.com', 80, $errno, $errstr, 30);

        if (!$socket) {
            return array(false, 'An Internet connection is needed for these tests');
        }

        fclose($socket);

        return array(true, 'OK');
    }
}
