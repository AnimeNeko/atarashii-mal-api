<?php

namespace Atarashii\APIBundle\Tests\Controller;

use Atarashii\APIBundle\Tests\Util\ConnectivityUtilities;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class RecordControllerTest.
 */
class RecordControllerTest extends WebTestCase
{
    private $client = null;

    public function testGetAction()
    {
        $client = $this->client;
        
        AnimeRecordTest::testGetAction($this, $client);
        MangaRecordTest::testGetAction($this, $client);
    }

    public function testGetActionPersonal()
    {
        $client = $this->client;

        AnimeRecordTest::testGetActionPersonal($this, $client);
        MangaRecordTest::testGetActionPersonal($this, $client);
    }

    public function testGetCastAction()
    {
        $client = $this->client;

        AnimeRecordTest::testGetCastAction($this, $client);
        MangaRecordTest::testGetCastAction($this, $client);
    }

    public function testGetScheduleAction()
    {
        $client = $this->client;
        
        $client->request('GET', '/2.1/anime/schedule');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertEquals(200, $statusCode);
        $this->assertNotNull($content);

        $item = $content->monday;
        $item = $item[0];

        $this->assertInternalType('int', $item->id);
        $this->assertInternalType('string', $item->title);
        $this->assertContains('cdn.myanimelist.net', $item->image_url);
        $this->assertInternalType('int', $item->episodes);
        $this->assertInternalType('string', $item->genres[0]);
        $this->assertInternalType('float', $item->members_score);
        $this->assertInternalType('int', $item->members_count);
        $this->assertInternalType('string', $item->synopsis);
        $this->assertInternalType('string', $item->producers[0]);
    }

    public function testGetReviewsAction()
    {
        $client = $this->client;

        AnimeRecordTest::testGetReviewsAction($this, $client);
        MangaRecordTest::testGetReviewsAction($this, $client);
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
