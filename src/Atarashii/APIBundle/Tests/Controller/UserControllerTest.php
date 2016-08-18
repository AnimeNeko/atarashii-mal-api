<?php

namespace Atarashii\APIBundle\Tests\Controller;

use Atarashii\APIBundle\Tests\Util\ConnectivityUtilities;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UserControllerTest.
 */
class UserControllerTest extends WebTestCase
{
    private $client = null;

    public function testProfile()
    {
        $client = $this->client;

        $client->request('GET', '/2/profile/AtarashiiApiTest');

        $rawContent = $client->getResponse()->getContent();
        $content = json_decode($rawContent);

        $this->assertNotNull($content);

        $this->assertNotNull($content->details->last_online); //This value changes based on the relative time MAL provides, so we can only check that something exists.
        $this->assertEquals('Female', $content->details->gender);
        $this->assertEquals('December 1, 1983', $content->details->birthday);
        $this->assertEquals('Hinata Inn', $content->details->location);
        $this->assertEquals('http://api.atarashiiapp.com', $content->details->website);
        $this->assertEquals('Member', $content->details->access_rank);

        $this->assertGreaterThanOrEqual(0.1, $content->anime_stats->time_days);
        $this->assertInternalType('int', $content->anime_stats->watching);
        $this->assertInternalType('int', $content->anime_stats->completed);
        $this->assertInternalType('int', $content->anime_stats->on_hold);
        $this->assertInternalType('int', $content->anime_stats->dropped);
        $this->assertInternalType('int', $content->anime_stats->plan_to_watch);
        $this->assertInternalType('int', $content->anime_stats->total_entries);
        $this->assertGreaterThanOrEqual(1, $content->anime_stats->total_entries);

        $this->assertGreaterThanOrEqual(0.1, $content->manga_stats->time_days);
        $this->assertInternalType('int', $content->manga_stats->reading);
        $this->assertInternalType('int', $content->manga_stats->completed);
        $this->assertInternalType('int', $content->manga_stats->on_hold);
        $this->assertInternalType('int', $content->manga_stats->dropped);
        $this->assertInternalType('int', $content->manga_stats->plan_to_read);
        $this->assertInternalType('int', $content->manga_stats->total_entries);
        $this->assertGreaterThanOrEqual(1, $content->manga_stats->total_entries);
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
