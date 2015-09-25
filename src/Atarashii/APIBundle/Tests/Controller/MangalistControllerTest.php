<?php

namespace Atarashii\APIBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Atarashii\APIBundle\Tests\Util\ConnectivityUtilities;

/**
 * Class MangalistControllerTest
 * @package Atarashii\APIBundle\Tests\Controller
 */
class MangalistControllerTest extends WebTestCase
{
    private $client = null;

    public function testGetAction()
    {
        $client = $this->client;

        //Check response for an invalid user
        $client->request('GET', '/2/mangalist/999999999999999999999');
        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);

        $this->assertStringStartsWith('Failed to find', $content->error);


        //Now, do all the checks with the reference user
        $client->request('GET', '/2/mangalist/AtarashiiApiTest');
        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);


        $this->assertGreaterThanOrEqual(1, count($content->manga));

        //There isn't a ton here to check, so just make sure our details has
        //data and our reference title is in the list
        $this->assertInternalType('float', $content->statistics->days);
        $this->assertGreaterThanOrEqual(0.1, $content->statistics->days);

        foreach ($content->manga as $listItem) {
            if ($listItem->id === 436) {
                $mangaItem = $listItem;
                break;
            }
        }

        $this->assertEquals('Uzumaki', $mangaItem->title);
        $this->assertEquals('completed', $mangaItem->read_status);
        $this->assertInternalType('int', $mangaItem->score);
        $this->assertEquals(9, $mangaItem->score);
   }

    public static function setUpBeforeClass() {
        $client = static::createClient();
        $container = $client->getContainer();

        $doTest = ConnectivityUtilities::checkConnection($container);

        if ($doTest[0] === false) {
            self::markTestSkipped($doTest[1]);
        }
    }

    protected function setUp() {
        $this->client = static::createClient();
    }

}