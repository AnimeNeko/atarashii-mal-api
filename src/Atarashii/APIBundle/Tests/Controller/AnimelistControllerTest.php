<?php

namespace Atarashii\APIBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Atarashii\APIBundle\Tests\Util\ConnectivityUtilities;

/**
 * Class AnimelistControllerTest.
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

    public function testAddAction()
    {
        $client = $this->client;

        $credentials = ConnectivityUtilities::getLoginCredentials($client->getContainer());

        if ($credentials !== false) {
            $animeID = 5941; //Cross Game
            $status = 1; //Watching
            $episodes = 3;
            $score = 8;

            $client->request('POST', '/2/animelist/anime',
                array(
                    'anime_id' => $animeID,
                    'status' => $status,
                    'episodes' => $episodes,
                    'score' => $score,
                ),
                array(),
                array(
                    'PHP_AUTH_USER' => $credentials['username'],
                    'PHP_AUTH_PW' => $credentials['password'],
                )
            );

            $rawContent = $client->getResponse()->getContent();
            $content = json_decode($rawContent);

            $this->assertNotNull($content);
            $this->assertTrue($client->getResponse()->isSuccessful());
            $this->assertEquals('ok', $content);

            //Make sure the title actually was added to the list
            $client->request('GET', '/2/animelist/'.$credentials['username']);
            $rawContent = $client->getResponse()->getContent();
            $content = json_decode($rawContent);

            $this->assertNotNull($content);
            $this->assertTrue($client->getResponse()->isSuccessful());

            $this->assertGreaterThanOrEqual(1, count($content->anime));

            foreach ($content->anime as $listItem) {
                if ($listItem->id === $animeID) {
                    $animeItem = $listItem;
                    break;
                }
            }

            $this->assertEquals('watching', $animeItem->watched_status);
            $this->assertInternalType('int', $animeItem->score);
            $this->assertEquals($score, $animeItem->score);
            $this->assertInternalType('int', $animeItem->watched_episodes);
            $this->assertEquals($episodes, $animeItem->watched_episodes);
        } else {
            $this->markTestSkipped('Username and password must be set.');
        }
    }

    /**
     * @depends testAddAction
     */
    public function testUpdateAction()
    {
        $client = $this->client;

        $credentials = ConnectivityUtilities::getLoginCredentials($client->getContainer());

        if ($credentials !== false) {
            $animeID = 5941; //Cross Game
            $status = 3; //on-hold
            $episodes = 5;
            $score = 7;
            $start = '2015-09-01';
            $end = '2015-10-30';
            $downloadedEps = $episodes;
            $storageType = 2; // DVD/CD
            $storageAmt = 4;
            $priority = 2; //High
            $rewatchValue = 3; //Medium
            $tags = 'shonen, sports, baseball';
            $comments = 'None';
            $fansubber = 'None';
            $isRewatching = 0;
            $rewatchCount = 2;

            $client->request('PUT', '/2/animelist/anime/'.$animeID,
                array(
                    'status' => $status, 'episodes' => $episodes, 'score' => $score, 'start' => $start, 'end' => $end,
                    'downloaded_eps' => $downloadedEps, 'storage_type' => $storageType, 'storage_amt' => $storageAmt,
                    'priority' => $priority, 'rewatch_value' => $rewatchValue, 'tags' => $tags, 'comments' => $comments,
                    'fansubber' => $fansubber, 'is_rewatching' => $isRewatching, 'rewatch_count' => $rewatchCount,
                ),
                array(),
                array(
                    'PHP_AUTH_USER' => $credentials['username'],
                    'PHP_AUTH_PW' => $credentials['password'],
                )
            );

            $this->assertTrue($client->getResponse()->isSuccessful());

            //Grab personal details for the title to check values
            $client->request('GET', '/2/anime/'.$animeID, array('mine' => 1), array(), array(
                'PHP_AUTH_USER' => $credentials['username'],
                'PHP_AUTH_PW' => $credentials['password'],
            ));

            $rawContent = $client->getResponse()->getContent();
            $content = json_decode($rawContent);

            $this->assertTrue($client->getResponse()->isSuccessful());

            $this->assertEquals('on-hold', $content->watched_status);
            $this->assertEquals($episodes, $content->watched_episodes);

            $this->assertInternalType('int', $content->score);
            $this->assertEquals($score, $content->score);

            $this->assertEquals($start, $content->watching_start);
            $this->assertEquals($rewatchCount, $content->rewatch_count);
        } else {
            $this->markTestSkipped('Username and password must be set.');
        }
    }

    /**
     * @depends testUpdateAction
     */
    public function testDeleteAction()
    {
        $client = $this->client;

        $credentials = ConnectivityUtilities::getLoginCredentials($client->getContainer());

        if ($credentials !== false) {
            $animeID = 5941; //Cross Game

            $client->request('DELETE', '/2/animelist/anime/'.$animeID,
                array(),
                array(),
                array(
                    'PHP_AUTH_USER' => $credentials['username'],
                    'PHP_AUTH_PW' => $credentials['password'],
                )
            );

            $this->assertTrue($client->getResponse()->isSuccessful());

            //Make sure the title actually was deleted
            $client->request('GET', '/2/animelist/'.$credentials['username']);
            $rawContent = $client->getResponse()->getContent();
            $content = json_decode($rawContent);

            $this->assertNotNull($content);
            $this->assertTrue($client->getResponse()->isSuccessful());

            $foundItem = false;

            foreach ($content->anime as $listItem) {
                if ($listItem->id === $animeID) {
                    $foundItem = true;
                    break;
                }
            }

            $this->assertFalse($foundItem);
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
