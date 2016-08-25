<?php

namespace Atarashii\APIBundle\Tests\Controller;

use Atarashii\APIBundle\Tests\Util\ConnectivityUtilities;

/**
 * Class MangaControllerTest.
 */
class MangaRecordTest
{
    public static function testGetAction($main, $client)
    {
        //First, test that non-existent titles are handled correctly
        $client->request('GET', '/2/manga/999999999999999');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $main->assertNotNull($content);
        $main->assertEquals(404, $statusCode);
        $main->assertStringStartsWith('not-found', $content->error);

        //Test an actual title, in this case Rosario + Vampire.
        $client->request('GET', '/2/manga/894');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $main->assertNotNull($content);
        $main->assertEquals(200, $statusCode);

        $main->assertEquals(894, $content->id);
        $main->assertEquals('Rosario to Vampire', $content->title);
        $main->assertContains('ロザリオとバンパイア', $content->other_titles->japanese);
        $main->assertContains('Rosario and Vampire', $content->other_titles->synonyms);

        $main->assertInternalType('int', $content->rank);
        $main->assertInternalType('int', $content->popularity_rank);

        $main->assertStringStartsWith('https://myanimelist.cdn-dena.com/images', $content->image_url);

        $main->assertEquals('Manga', $content->type);

        $main->assertEquals(40, $content->chapters);
        $main->assertEquals(10, $content->volumes);

        $main->assertEquals('finished', $content->status);

        $main->assertInternalType('float', $content->members_score);
        $main->assertGreaterThan(6, $content->members_score);
        $main->assertInternalType('int', $content->members_count);
        $main->assertGreaterThan(20000, $content->members_count);
        $main->assertInternalType('int', $content->favorited_count);
        $main->assertGreaterThan(600, $content->favorited_count);

        $main->assertStringStartsWith('Aono Tsukune is so hard', $content->synopsis);

        $main->assertContains('Comedy', $content->genres);
        $main->assertContains('Vampire', $content->genres);

        $main->assertGreaterThanOrEqual(1, count($content->anime_adaptations));

        $main->assertGreaterThanOrEqual(1, count($content->related_manga));
    }

    public static function testGetActionPersonal($main, $client)
    {
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

            $main->assertNotNull($content);
            $main->assertEquals(200, $statusCode);

            $main->assertInternalType('int', $content->score);
            $main->assertEquals(9, $content->score);

            $main->assertEquals('completed', $content->read_status);
            $main->assertEquals('2013-09-17', $content->reading_start);
            $main->assertEquals('2013-11-17', $content->reading_end);
        } else {
            $main->markTestSkipped('Username and password must be set.');
        }
    }

    public static function testGetCastAction($main, $client)
    {
        //Test an actual title, in this case Rosario + Vampire.
        $client->request('GET', '/2/manga/cast/894');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $main->assertNotNull($content);
        $main->assertEquals(200, $statusCode);

        $characterList = $content->Characters;

        //Moka
        foreach ($characterList as $characterItem) {
            if ($characterItem->id == 3861) {
                $character = $characterItem;
                break;
            }
        }

        //We're already testing the parser directly, so just make sure we got some valid info.
        $main->assertInstanceOf('stdClass', $character);
        $main->assertEquals('Akashiya, Moka', $character->name);
    }

    public static function testGetReviewsAction($main, $client)
    {
        //Test an actual title, in this case Rosario + Vampire.
        $client->request('GET', '/2/manga/reviews/894');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $main->assertNotNull($content);
        $main->assertEquals(200, $statusCode);

        $review = $content[0];

        $main->assertNotNull($review->date);
        $main->assertNotNull($review->username);

        //Quick date check
        $date = new \DateTime($review->date);
        $main->assertInstanceOf('DateTime', $date);

        //Making sure the date we grabbed was properly parsed and matches with the source
        $date = $date->format('Y-m-d');
        $main->assertEquals($review->date, $date);
    }
}
