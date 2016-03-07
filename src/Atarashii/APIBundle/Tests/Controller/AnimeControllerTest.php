<?php

namespace Atarashii\APIBundle\Tests\Controller;

use Atarashii\APIBundle\Tests\Util\ConnectivityUtilities;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AnimeControllerTest.
 */
class AnimeControllerTest extends WebTestCase
{
    private $client = null;

    public function testGetAction()
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

    public function testGetActionPersonal()
    {
        $client = $this->client;

        $credentials = ConnectivityUtilities::getLoginCredentials($client->getContainer());

        if ($credentials !== false) {

            //Now, test grabbing personal details for a title
            $client->request('GET', '/2/anime/189', array('mine' => 1), array(), array(
                'PHP_AUTH_USER' => $credentials['username'],
                'PHP_AUTH_PW' => $credentials['password'],
            ));

            $rawContent = $client->getResponse()->getContent();
            $statusCode = $client->getResponse()->getStatusCode();
            $content = json_decode($rawContent);

            $this->assertNotNull($content);
            $this->assertEquals(200, $statusCode);

            $this->assertInternalType('int', $content->score);
            $this->assertEquals(7, $content->score);

            $this->assertEquals('completed', $content->watched_status);
            $this->assertEquals('2000-01-01', $content->watching_start);
            $this->assertEquals('2000-03-20', $content->watching_end);
        } else {
            $this->markTestSkipped('Username and password must be set.');
        }
    }

    public function testGetCastAction()
    {
        $client = $this->client;

        //Test an actual title, in this case NouCome.
        $client->request('GET', '/2/anime/cast/19221');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);

        $characterList = $content->Characters;

        //Yukihira Furano
        foreach ($characterList as $characterItem) {
            if ($characterItem->id == 81223) {
                $character = $characterItem;
                break;
            }
        }

        $staffList = $content->Staff;

        //Sadohara Kaori
        foreach ($staffList as $staffItem) {
            if ($staffItem->id == 12284) {
                $staff = $staffItem;
                break;
            }
        }

        //We're already testing the parser directly, so just make sure we got some valid info.
        $this->assertInstanceOf('stdClass', $character);
        $this->assertEquals('Yukihira, Furano', $character->name);

        $this->assertInstanceOf('stdClass', $staff);
        $this->assertEquals('Sadohara, Kaori', $staff->name);
    }

    public function testGetReviewsAction()
    {
        $client = $this->client;

        //Test an actual title, in this case NouCome.
        $client->request('GET', '/2/anime/reviews/19221');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);

        $review = $content[0];

        $this->assertNotNull($review->date);
        $this->assertNotNull($review->username);

        //Quick date check
        $date = new \DateTime($review->date);
        $this->assertInstanceOf('DateTime', $date);

        //Making sure the date we grabbed was properly parsed and matches with the source
        $date = $date->format('Y-m-d');
        $this->assertEquals($review->date, $date);
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
