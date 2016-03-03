<?php

namespace Atarashii\APIBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Atarashii\APIBundle\Tests\Util\ConnectivityUtilities;

/**
 * Class MangalistControllerTest.
 */
class MangalistControllerTest extends WebTestCase
{
    private $client = null;

    public function testGetAction()
    {
        $client = $this->client;

        //Check response for an invalid user
        $client->request('GET', '/2/mangalist/999999999999999999999');
        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);

        $this->assertStringStartsWith('Failed to find', $content->error);

        //Now, do all the checks with the reference user
        $client->request('GET', '/2/mangalist/AtarashiiApiTest');
        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);

        $this->assertGreaterThanOrEqual(1, count($content->manga));

        //There isn't a ton here to check, so just make sure our details has
        //data and our reference title is in the list
        $this->assertInternalType('float', $content->statistics->days);
        $this->assertGreaterThanOrEqual(0.1, $content->statistics->days);

        foreach ($content->manga as $listItem) {
            if ($listItem->id === 436) {
                $mangaItem = $listItem;
                break;
            }
        }

        $this->assertEquals('Uzumaki', $mangaItem->title);
        $this->assertEquals('completed', $mangaItem->read_status);
        $this->assertInternalType('int', $mangaItem->score);
        $this->assertEquals(9, $mangaItem->score);
    }

    public function testAddAction()
    {
        $client = $this->client;

        $credentials = ConnectivityUtilities::getLoginCredentials($client->getContainer());

        if ($credentials !== false) {
            $mangaID = 107; //Chobits
            $status = 1; //Reading
            $chapters = 5;
            $volumes = 1;
            $score = 8;

            $client->request('POST', '/2/mangalist/manga',
                array(
                    'manga_id' => $mangaID,
                    'status' => $status,
                    'chapters' => $chapters,
                    'volumes' => $volumes,
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
            $client->request('GET', '/2/mangalist/'.$credentials['username']);
            $rawContent = $client->getResponse()->getContent();
            $content = json_decode($rawContent);

            $this->assertNotNull($content);
            $this->assertTrue($client->getResponse()->isSuccessful());

            $this->assertGreaterThanOrEqual(1, count($content->manga));

            foreach ($content->manga as $listItem) {
                if ($listItem->id === $mangaID) {
                    $mangaItem = $listItem;
                    break;
                }
            }

            $this->assertEquals('reading', $mangaItem->read_status);

            $this->assertInternalType('int', $mangaItem->score);
            $this->assertEquals($score, $mangaItem->score);

            $this->assertInternalType('int', $mangaItem->chapters_read);
            $this->assertEquals($chapters, $mangaItem->chapters_read);

            $this->assertInternalType('int', $mangaItem->volumes_read);
            $this->assertEquals($volumes, $mangaItem->volumes_read);
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
            $mangaID = 107; //Chobits
            $status = 3; //On-hold
            $chapters = 15;
            $volumes = 3;
            $score = 7;
            $start = '2015-09-01';

            $client->request('PUT', '/2/mangalist/manga/'.$mangaID,
                array(
                    'manga_id' => $mangaID,
                    'status' => $status,
                    'chapters' => $chapters,
                    'volumes' => $volumes,
                    'score' => $score,
                    'start' => $start,
                ),
                array(),
                array(
                    'PHP_AUTH_USER' => $credentials['username'],
                    'PHP_AUTH_PW' => $credentials['password'],
                )
            );

            $this->assertTrue($client->getResponse()->isSuccessful());

            //Grab personal details for the title to check values
            $client->request('GET', '/2/manga/'.$mangaID, array('mine' => 1), array(), array(
                'PHP_AUTH_USER' => $credentials['username'],
                'PHP_AUTH_PW' => $credentials['password'],
            ));

            $rawContent = $client->getResponse()->getContent();
            $content = json_decode($rawContent);

            $this->assertTrue($client->getResponse()->isSuccessful());

            $this->assertEquals('on-hold', $content->read_status);

            $this->assertInternalType('int', $content->score);
            $this->assertEquals($score, $content->score);

            $this->assertInternalType('int', $content->chapters_read);
            $this->assertEquals($chapters, $content->chapters_read);

            $this->assertInternalType('int', $content->volumes_read);
            $this->assertEquals($volumes, $content->volumes_read);

            $this->assertEquals($start, $content->reading_start);
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
            $mangaID = 107; //Chobits

            $client->request('DELETE', '/2/mangalist/manga/'.$mangaID,
                array(),
                array(),
                array(
                    'PHP_AUTH_USER' => $credentials['username'],
                    'PHP_AUTH_PW' => $credentials['password'],
                )
            );

            $this->assertTrue($client->getResponse()->isSuccessful());

            //Make sure the title actually was deleted
            $client->request('GET', '/2/mangalist/'.$credentials['username']);
            $rawContent = $client->getResponse()->getContent();
            $content = json_decode($rawContent);

            $this->assertNotNull($content);
            $this->assertTrue($client->getResponse()->isSuccessful());

            $foundItem = false;

            foreach ($content->manga as $listItem) {
                if ($listItem->id === $mangaID) {
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
