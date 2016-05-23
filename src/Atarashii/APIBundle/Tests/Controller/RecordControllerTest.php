<?php

namespace Atarashii\APIBundle\Tests\Controller;

use Atarashii\APIBundle\Tests\Util\ConnectivityUtilities;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AnimeRecordTest.
 */
class RecordControllerTest extends WebTestCase
{
    private $client = null;

    public function testGetAction()
    {
        $client = $this->client;
        
        AnimeRecordTest::testGetAction($this, $client);
        MangaRecordTest::testGetAction($this, $client);
    }

    public function testGetActionPersonal()
    {
        $client = $this->client;

        AnimeRecordTest::testGetActionPersonal($this, $client);
        MangaRecordTest::testGetActionPersonal($this, $client);
    }

    public function testGetCastAction()
    {
        $client = $this->client;

        AnimeRecordTest::testGetCastAction($this, $client);
        MangaRecordTest::testGetCastAction($this, $client);
    }

    public function testGetReviewsAction()
    {
        $client = $this->client;

        AnimeRecordTest::testGetReviewsAction($this, $client);
        MangaRecordTest::testGetReviewsAction($this, $client);
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
