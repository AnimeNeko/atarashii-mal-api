<?php

namespace Atarashii\APIBundle\Tests\Controller;

use Atarashii\APIBundle\Tests\Util\ConnectivityUtilities;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PersonControllerTest extends WebTestCase
{
    private $client = null;

    public function testGetAction()
    {
        $client = $this->client;

        //First, test that non-existent people are handled correctly
        $client->request('GET', '/2.1/people/999999999999999');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);
        $this->assertStringStartsWith('not-found', $content->error);

        // Test actual people...

        // Test Kana Hanazawa (a "normal" profile)
        $client->request('GET', '/2.1/people/185');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);

        $this->assertEquals(185, $content->id);
        $this->assertEquals('https://myanimelist.cdn-dena.com/images/voiceactors/3/43500.jpg', $content->image_url);
        $this->assertEquals('Hanazawa, Kana', $content->name);
        $this->assertEquals('香菜', $content->given_name);
        $this->assertEquals('花澤', $content->family_name);
        $this->assertEquals('1989-02-25', $content->birthday);
        $this->assertEquals('http://www.hanazawakana-music.net/', $content->website_url);
        $this->assertInternalType('int', $content->favorited_count);
        $this->assertContains('Height: 156 cm', $content->more_details);

        $this->assertGreaterThan(250, count($content->voice_acting_roles));
        $this->assertGreaterThanOrEqual(30, count($content->anime_staff_positions));

        // Test Miyazaki Hayao ("normal" with published manga)
        $client->request('GET', '/2.1/people/1870');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);

        $this->assertGreaterThan(10, count($content->published_manga));

        // Test Johnny Yong Bosch ("normal" with alternate names)
        $client->request('GET', '/2.1/people/10');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);

        $this->assertGreaterThanOrEqual(1, count($content->alternate_names));

        // Test huke (interesting case for family name parsing)
        $client->request('GET', '/2.1/people/10145');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);

        $this->assertEquals('huke', $content->family_name);
        $this->assertEquals('', $content->given_name);

        // Test Ikimono-gakari (birthday, year & month only)
        $client->request('GET', '/2.1/people/7277');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);

        $this->assertEquals('1999-02', $content->birthday);

        // Test ClairS (birthday, year only)
        $client->request('GET', '/2.1/people/11746');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);

        $this->assertEquals('2009', $content->birthday);

        // Test Miyamoto Kano (birthday, month and day only)
        $client->request('GET', '/2.1/people/2608');

        $rawContent = $client->getResponse()->getContent();
        $statusCode = $client->getResponse()->getStatusCode();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);
        $this->assertEquals(200, $statusCode);

        $this->assertEquals('-06-15', $content->birthday);
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
