<?php

namespace Atarashii\APIBundle\Tests\Controller;

use Atarashii\APIBundle\Tests\Util\ConnectivityUtilities;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class MangaControllerTest.
 */
class MangaControllerTest extends WebTestCase
{
    private $client = null;

    public function testGetAction()
    {
        $client = $this->client;

        //First, test that non-existent titles are handled correctly
        $client->request('GET', '/2/manga/999999999999999');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(404, $statusCode);
        $this->assertStringStartsWith('No manga found', $content->error);

        //Test an actual title, in this case NouCome.
        $client->request('GET', '/2/manga/894');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);

        $this->assertEquals(894, $content->id);
        $this->assertEquals('Rosario to Vampire', $content->title);
        $this->assertContains('ロザリオとバンパイア', $content->other_titles->japanese);
        $this->assertContains('Rosario and Vampire', $content->other_titles->synonyms);

        $this->assertInternalType('int', $content->rank);
        $this->assertInternalType('int', $content->popularity_rank);

        $this->assertStringStartsWith('http://cdn.myanimelist.net/images', $content->image_url);

        $this->assertEquals('Manga', $content->type);

        $this->assertEquals(40, $content->chapters);
        $this->assertEquals(10, $content->volumes);

        $this->assertEquals('finished', $content->status);

        $this->assertInternalType('float', $content->members_score);
        $this->assertGreaterThan(6, $content->members_score);
        $this->assertInternalType('int', $content->members_count);
        $this->assertGreaterThan(20000, $content->members_count);
        $this->assertInternalType('int', $content->favorited_count);
        $this->assertGreaterThan(600, $content->favorited_count);

        $this->assertStringStartsWith('Aono Tsukune is so hard', $content->synopsis);

        $this->assertContains('Comedy', $content->genres);
        $this->assertContains('Vampire', $content->genres);

        $this->assertGreaterThanOrEqual(1, count($content->anime_adaptations));

        $this->assertGreaterThanOrEqual(1, count($content->related_manga));
    }

    public function testGetActionPersonal()
    {
        $client = $this->client;

        $credentials = ConnectivityUtilities::getLoginCredentials($client->getContainer());

        if ($credentials !== false) {

            //Now, test grabbing personal details for a title
            $client->request('GET', '/2/manga/436', array('mine' => 1), array(), array(
                'PHP_AUTH_USER' => $credentials['username'],
                'PHP_AUTH_PW' => $credentials['password'],
            ));

            $rawContent = $client->getResponse()->getContent();
            $statusCode = $client->getResponse()->getStatusCode();
            $content = json_decode($rawContent);

            $this->assertNotNull($content);
            $this->assertEquals(200, $statusCode);

            $this->assertInternalType('int', $content->score);
            $this->assertEquals(9, $content->score);

            $this->assertEquals('completed', $content->read_status);
            $this->assertEquals('2013-09-17', $content->reading_start);
            $this->assertEquals('2013-11-17', $content->reading_end);
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
