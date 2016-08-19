<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Parser\EpsParser;

/**
 * Class AnimeTest.
 *
 * @coversDefaultClass Atarashii\APIBundle\Parser\EpsParser
 */
class EpsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::parse
     */
    public function testParse()
    {
        $epsContents = file_get_contents(__DIR__.'/../InputSamples/anime-21-eps.html');
        $apiVersion = '2.1';

        $epsArray = EpsParser::parse($epsContents, $apiVersion);

        $this->assertInternalType('array', $epsArray);

        $eps = $epsArray[0];

        /* As this test is against a static downloaded copy, we know what the exact values should be. As such, rather
         * than testing based on type for many of the items, we're checking for exact values. Obviously, this test
         * will need to be updated when the source file is re-downloaded to update any values.
         */
        $this->assertInstanceOf('Atarashii\APIBundle\Model\Episode', $eps);

        $this->assertEquals('I\'m Luffy! The Man Who\'s Gonna Be King of the Pirates!', $eps->getTitle());
        $this->assertInstanceOf('\DateTime', new \DateTime($eps->getAirDate()));
        $this->assertGreaterThan(0, $eps->getNumber());

        $otherTitles = $eps->getOtherTitles();

        $this->assertArrayHasKey('english', $otherTitles);
        $this->assertEquals('Ore wa Luffy! Kaizoku Ou ni Naru Otoko Da!', $otherTitles['english'][0]);

        $this->assertArrayHasKey('japanese', $otherTitles);
        $this->assertEquals('俺はルフィ!海賊王になる男だ!', $otherTitles['japanese'][0]);
    }
}
