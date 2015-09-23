<?php

namespace Atarashii\APIBundle\Tests\Controller;

use Atarashii\APIBundle\Tests\Util\ConnectivityUtilities;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class VerifyControllerTest
 * @package Atarashii\APIBundle\Tests\Controller
 */
class AnimeControllerTest extends WebTestCase
{
    private $client = null;

    public function testVerifyAction()
    {
        $client = $this->client;

        //First, test that non-existent titles are handled correctly
        $client->request('GET', '/2/anime/999999999999999');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(404, $statusCode);
        $this->assertStringStartsWith('No series found', $content->error);


        //Test an actual title, in this case NouCome.
        $client->request('GET', '/2/anime/19221');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);

        $this->assertEquals(19221, $content->id);
        $this->assertEquals('Ore no Nounai Sentakushi ga, Gakuen Love Comedy wo Zenryoku de Jama Shiteiru', $content->title);
        $this->assertContains('俺の脳内選択肢が、学園ラブコメを全力で邪魔している', $content->other_titles->japanese);
        $this->assertContains('NouCome', $content->other_titles->synonyms);

        $this->assertInternalType('int', $content->rank);
        $this->assertInternalType('int', $content->popularity_rank);

        $this->assertStringStartsWith('http://cdn.myanimelist.net/images', $content->image_url);

        $this->assertEquals('TV', $content->type);

        $this->assertEquals(10, $content->episodes);

        $this->assertEquals('finished airing', $content->status);

        $this->assertEquals('2013-10-10', $content->start_date);
        $this->assertEquals('2013-12-12', $content->end_date);

        $this->assertEquals('PG-13 - Teens 13 or older', $content->classification);

        $this->assertInternalType('float', $content->members_score);
        $this->assertGreaterThan(6, $content->members_score);
        $this->assertInternalType('int', $content->members_count);
        $this->assertGreaterThan(100000, $content->members_count);
        $this->assertInternalType('int', $content->favorited_count);
        $this->assertGreaterThan(600, $content->favorited_count);

        $this->assertStringStartsWith('Kanade Amakusa is a high school student', $content->synopsis);

        $this->assertContains('Mages', $content->producers);
        $this->assertContains('Comedy', $content->genres);

        $this->assertGreaterThanOrEqual(1, count($content->manga_adaptations));

        $this->assertEquals(0, count($content->prequels));
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