<?php

namespace Atarashii\APIBundle\Tests\Controller;

use Atarashii\APIBundle\Tests\Util\ConnectivityUtilities;

/**
 * Class AnimeControllerTest.
 */
class AnimeRecordTest
{
    public static function testGetAction($main, $client)
    {
        //First, test that non-existent titles are handled correctly
        $client->request('GET', '/2/anime/999999999999999');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $main->assertNotNull($content);
        $main->assertEquals(404, $statusCode);
        $main->assertStringStartsWith('not-found', $content->error);

        //Test an actual title, in this case NouCome.
        $client->request('GET', '/2/anime/19221');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $main->assertNotNull($content);
        $main->assertEquals(200, $statusCode);

        $main->assertEquals(19221, $content->id);
        $main->assertEquals('Ore no Nounai Sentakushi ga, Gakuen Love Comedy wo Zenryoku de Jama Shiteiru', $content->title);
        $main->assertContains('俺の脳内選択肢が、学園ラブコメを全力で邪魔している', $content->other_titles->japanese);
        $main->assertContains('NouCome', $content->other_titles->synonyms);

        $main->assertInternalType('int', $content->rank);
        $main->assertInternalType('int', $content->popularity_rank);

        $main->assertStringStartsWith('https://myanimelist.cdn-dena.com/images', $content->image_url);

        $main->assertEquals('TV', $content->type);

        $main->assertEquals(10, $content->episodes);

        $main->assertEquals('finished airing', $content->status);

        $main->assertEquals('2013-10-10', $content->start_date);
        $main->assertEquals('2013-12-12', $content->end_date);

        $main->assertEquals('PG-13 - Teens 13 or older', $content->classification);

        $main->assertInternalType('float', $content->members_score);
        $main->assertGreaterThan(6, $content->members_score);
        $main->assertInternalType('int', $content->members_count);
        $main->assertGreaterThan(100000, $content->members_count);
        $main->assertInternalType('int', $content->favorited_count);
        $main->assertGreaterThan(600, $content->favorited_count);

        $main->assertStringStartsWith('Kanade Amakusa is a high school student', $content->synopsis);

        $main->assertContains('MAGES.', $content->producers);
        $main->assertContains('Comedy', $content->genres);

        $main->assertGreaterThanOrEqual(1, count($content->manga_adaptations));

        $main->assertEquals(0, count($content->prequels));
    }

    public static function testGetActionPersonal($main, $client)
    {
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

            $main->assertNotNull($content);
            $main->assertEquals(200, $statusCode);

            $main->assertInternalType('int', $content->score);
            $main->assertEquals(7, $content->score);

            $main->assertEquals('completed', $content->watched_status);
            $main->assertEquals('2000-01-01', $content->watching_start);
            $main->assertEquals('2000-03-20', $content->watching_end);
        } else {
            $main->markTestSkipped('Username and password must be set.');
        }
    }

    public static function testGetCastAction($main, $client)
    {
        //Test an actual title, in this case NouCome.
        $client->request('GET', '/2/anime/cast/19221');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $main->assertNotNull($content);
        $main->assertEquals(200, $statusCode);

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
        $main->assertInstanceOf('stdClass', $character);
        $main->assertEquals('Yukihira, Furano', $character->name);

        $main->assertInstanceOf('stdClass', $staff);
        $main->assertEquals('Sadohara, Kaori', $staff->name);
    }

    public static function testGetReviewsAction($main, $client)
    {
        //Test an actual title, in this case NouCome.
        $client->request('GET', '/2/anime/reviews/19221');

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
