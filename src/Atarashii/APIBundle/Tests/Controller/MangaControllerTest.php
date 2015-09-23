<?php

namespace Atarashii\APIBundle\Tests\Controller;

use Atarashii\APIBundle\Tests\Util\ConnectivityUtilities;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class VerifyControllerTest
 * @package Atarashii\APIBundle\Tests\Controller
 */
class MangaControllerTest extends WebTestCase
{
    private $client = null;

    public function testVerifyAction()
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