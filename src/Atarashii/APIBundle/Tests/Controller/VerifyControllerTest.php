<?php

namespace Atarashii\APIBundle\Tests\Controller;

use Atarashii\APIBundle\Tests\Util\ConnectivityUtilities;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class VerifyControllerTest.
 */
class VerifyControllerTest extends WebTestCase
{
    private $client = null;

    public function testVerifyAction()
    {
        $client = $this->client;

        $credentials = ConnectivityUtilities::getLoginCredentials($client->getContainer());

        if ($credentials !== false) {

            // Make sure we get a 401 if we don't pass credentials
            $client->request('GET', '/2/account/verify_credentials');

            $rawContent = $client->getResponse()->getContent();
            $statusCode = $client->getResponse()->getStatusCode();
            $content = json_decode($rawContent);

            $this->assertNotNull($content);
            $this->assertEquals(401, $statusCode);
            $this->assertEquals('unauthorized', $content->error);

            // Log in properly this time, make sure we get a 200 with a response of "OK"
            $client->request('GET', '/2/account/verify_credentials', array(), array(), array(
                'PHP_AUTH_USER' => $credentials['username'],
                'PHP_AUTH_PW' => $credentials['password'],
            ));

            $rawContent = $client->getResponse()->getContent();
            $statusCode = $client->getResponse()->getStatusCode();
            $content = json_decode($rawContent);

            $this->assertNotNull($content);
            $this->assertEquals(200, $statusCode);
            $this->assertEquals('OK', $content->authorized);
        } else {
            $this->markTestSkipped('Username and password must be set.');
        }
    }

    public static function setUpBeforeClass()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $doTest = ConnectivityUtilities::checkConnection($container);

        if ($doTest[0] === false) {
            self::markTestSkipped($doTest[1]);
        }
    }

    protected function setUp()
    {
        $this->client = static::createClient();
    }
}
