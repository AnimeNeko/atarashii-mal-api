<?php

namespace Atarashii\APIBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Atarashii\APIBundle\Tests\Util\ConnectivityUtilities;

/**
 * Class AnimelistControllerTest
 * @package Atarashii\APIBundle\Tests\Controller
 */
class AnimelistControllerTest extends WebTestCase
{
    private $client = null;

    public function testGetAction()
    {
        $client = $this->client;

        //Check response for an invalid user
        $client->request('GET', '/2/animelist/999999999999999999999');
        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);

        $this->assertStringStartsWith('Failed to find', $content->error);


        //Now, do all the checks with the reference user
        $client->request('GET', '/2/animelist/AtarashiiApiTest');
        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);


        $this->assertGreaterThanOrEqual(1, count($content->anime));

        //There isn't a ton here to check, so just make sure our details has
        //data and our reference title is in the list
        $this->assertInternalType('float', $content->statistics->days);
        $this->assertGreaterThanOrEqual(0.1, $content->statistics->days);

        foreach ($content->anime as $listItem) {
            if ($listItem->id === 189) {
                $animeItem = $listItem;
                break;
            }
        }

        $this->assertEquals('Love Hina', $animeItem->title);
        $this->assertEquals('completed', $animeItem->watched_status);
        $this->assertInternalType('int', $animeItem->score);
        $this->assertEquals(7, $animeItem->score);
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