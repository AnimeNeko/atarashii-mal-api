<?php

namespace Atarashii\APIBundle\Tests\Controller;

use Atarashii\APIBundle\Tests\Util\ConnectivityUtilities;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TopControllerTest.
 */
class TopControllerTest extends WebTestCase
{
    private $client = null;

    public function testGetTopAnimeAction()
    {
        $client = $this->client;

        $client->request('GET', '/2.1/anime/top');

        $rawContent = $client->getResponse()->getContent();
        $content = json_decode($rawContent);

        $this->assertInternalType('array', $content);

        $sampleRecord = $content[0];

        //We don't know the exact title that will show up here, so do some basic
        //sanity checking of what properties and types we know will exist.
        $this->assertNotNull($sampleRecord->id);
        $this->assertInternalType('int', $sampleRecord->id);
        $this->assertGreaterThan(0, $sampleRecord->id);

        $this->assertNotNull($sampleRecord->title);
        $this->assertInternalType('string', $sampleRecord->title);

        $this->assertNotNull($sampleRecord->image_url);
        $this->assertContains('images/anime', $sampleRecord->image_url);

        $client->request('GET', '/2.1/anime/top', ['type' => 'movie']);

        $rawContent = $client->getResponse()->getContent();
        $content = json_decode($rawContent);

        $this->assertInternalType('array', $content);

        $sampleRecord = $content[0];

        //We don't know the exact title that will show up here, so do some basic
        //sanity checking of what properties and types we know will exist.
        $this->assertNotNull($sampleRecord->id);
        $this->assertInternalType('int', $sampleRecord->id);
        $this->assertGreaterThan(0, $sampleRecord->id);

        $this->assertNotNull($sampleRecord->title);
        $this->assertInternalType('string', $sampleRecord->title);

        $this->assertNotNull($sampleRecord->image_url);
        $this->assertContains('images/anime', $sampleRecord->image_url);
    }

    public function testGetTopMangaAction()
    {
        $client = $this->client;

        $client->request('GET', '/2.1/manga/top');

        $rawContent = $client->getResponse()->getContent();
        $content = json_decode($rawContent);

        $this->assertInternalType('array', $content);

        $sampleRecord = $content[0];

        //We don't know the exact title that will show up here, so do some basic
        //sanity checking of what properties and types we know will exist.
        $this->assertNotNull($sampleRecord->id);
        $this->assertInternalType('int', $sampleRecord->id);
        $this->assertGreaterThan(0, $sampleRecord->id);

        $this->assertNotNull($sampleRecord->title);
        $this->assertInternalType('string', $sampleRecord->title);

        $this->assertNotNull($sampleRecord->image_url);
        $this->assertContains('images/manga', $sampleRecord->image_url);

        $client->request('GET', '/2.1/manga/top', ['type' => 'manga']);

        $rawContent = $client->getResponse()->getContent();
        $content = json_decode($rawContent);

        $this->assertInternalType('array', $content);

        $sampleRecord = $content[0];

        //We don't know the exact title that will show up here, so do some basic
        //sanity checking of what properties and types we know will exist.
        $this->assertNotNull($sampleRecord->id);
        $this->assertInternalType('int', $sampleRecord->id);
        $this->assertGreaterThan(0, $sampleRecord->id);

        $this->assertNotNull($sampleRecord->title);
        $this->assertInternalType('string', $sampleRecord->title);

        $this->assertNotNull($sampleRecord->image_url);
        $this->assertContains('images/manga', $sampleRecord->image_url);
    }

    public function testGetPopularAnimeAction()
    {
        $client = $this->client;

        $client->request('GET', '/2.1/anime/popular');

        $rawContent = $client->getResponse()->getContent();
        $content = json_decode($rawContent);

        $this->assertInternalType('array', $content);

        $sampleRecord = $content[0];

        //We don't know the exact title that will show up here, so do some basic
        //sanity checking of what properties and types we know will exist.
        $this->assertNotNull($sampleRecord->id);
        $this->assertInternalType('int', $sampleRecord->id);
        $this->assertGreaterThan(0, $sampleRecord->id);

        $this->assertNotNull($sampleRecord->title);
        $this->assertInternalType('string', $sampleRecord->title);

        $this->assertNotNull($sampleRecord->image_url);
        $this->assertContains('images/anime', $sampleRecord->image_url);
    }

    public function testGetPopularMangaAction()
    {
        $client = $this->client;

        $client->request('GET', '/2.1/manga/popular');

        $rawContent = $client->getResponse()->getContent();
        $content = json_decode($rawContent);

        $this->assertInternalType('array', $content);

        $sampleRecord = $content[0];

        //We don't know the exact title that will show up here, so do some basic
        //sanity checking of what properties and types we know will exist.
        $this->assertNotNull($sampleRecord->id);
        $this->assertInternalType('int', $sampleRecord->id);
        $this->assertGreaterThan(0, $sampleRecord->id);

        $this->assertNotNull($sampleRecord->title);
        $this->assertInternalType('string', $sampleRecord->title);

        $this->assertNotNull($sampleRecord->image_url);
        $this->assertContains('images/manga', $sampleRecord->image_url);
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
