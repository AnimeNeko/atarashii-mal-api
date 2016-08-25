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

    public function testGetRecsAction()
    {
        $client = $this->client;

        // One Piece
        $client->request('GET', '/2.1/anime/recs/21');
        $this->checkRecs($client);
        
        // Death Note
        $client->request('GET', '/2.1/manga/recs/21');
        $this->checkRecs($client);
    }

    /**
     * Test records for phpUnit.
     * 
     * This function was created to reduce the amount of codes.
     * 
     * @param $client The passed client
     */
    private function checkRecs($client) {
        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertEquals(200, $statusCode);
        $this->assertNotNull($content);

        $this->assertInternalType('array', $content);
        $item = $content[0];

        $record = $item->item;

        $this->assertInternalType('int', $record->id);
        $this->assertGreaterThan(0, $record->id);
        $this->assertInternalType('string', $record->title);
        $this->assertContains('myanimelist.cdn-dena.com', $record->image_url);

        $recommendations = $item->recommendations;
        $this->assertInternalType('array', $recommendations);
        $recommendation = $recommendations[0];
        $this->assertInternalType('string', $recommendation->information);
        $this->assertInternalType('string', $recommendation->username);
    }

    public function testGetEpsAction()
    {
        $client = $this->client;

        $client->request('GET', '/2.1/anime/episodes/21');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertEquals(200, $statusCode);
        $this->assertNotNull($content);

        $this->assertInternalType('array', $content);
        $anime = $content[0];

        $this->assertInternalType('int', $anime->number);
        $this->assertGreaterThan(0, $anime->number);
        $this->assertInternalType('string', $anime->title);
        $this->assertEquals('I\'m Luffy! The Man Who\'s Gonna Be King of the Pirates!', $anime->title);
        $this->assertInstanceOf('\DateTime', new \DateTime($anime->air_date));

        $this->assertEquals('Ore wa Luffy! Kaizoku Ou ni Naru Otoko Da!', $anime->other_titles->english[0]);

        $this->assertEquals('俺はルフィ!海賊王になる男だ!', $anime->other_titles->japanese[0]);
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
        $this->assertInternalType('array', $item);
        
        $item = $item[0];

        $this->assertInternalType('int', $item->id);
        $this->assertGreaterThan(0, $item->id);
        $this->assertInternalType('string', $item->title);
        $this->assertContains('myanimelist.cdn-dena.com', $item->image_url);
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
