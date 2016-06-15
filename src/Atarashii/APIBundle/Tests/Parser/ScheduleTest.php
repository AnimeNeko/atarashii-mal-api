<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Parser\ScheduleParser;

/**
 * Class ScheduleTest.
 *
 * @coversDefaultClass Atarashii\APIBundle\Parser\ScheduleParser
 */
class ScheduleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::parse
     */
    public function testParse()
    {
        $contents = file_get_contents(__DIR__.'/../InputSamples/schedule-26052016.html');
        $apiVersion = '2.1';

        $schedule = ScheduleParser::parse($contents, $apiVersion);

        $this->assertInternalType('array', $schedule);
        $item = $schedule['monday'][0];

        /* As this test is against a static downloaded copy, we know what the exact values should be. As such, rather
         * than testing based on type for many of the items, we're checking for exact values. Obviously, this test
         * will need to be updated when the source file is re-downloaded to update any values.
         */

        $this->assertEquals(32601, $item->getId());
        $this->assertEquals('12-sai.: Chicchana Mune no Tokimeki', $item->getTitle());
        $this->assertEquals('http://cdn.myanimelist.net/images/anime/11/78391.jpg', $item->getImageUrl());
        $this->assertEquals(12, $item->getEpisodes());
        $this->assertContains('Romance', $item->getGenres()[0]);

        $this->assertInternalType('float', $item->getMembersScore());
        $this->assertGreaterThan(6.0, $item->getMembersScore());

        $this->assertInternalType('int', $item->getMembersCount());
        $this->assertEquals(8991, $item->getMembersCount());

        $this->assertStringStartsWith('The story begins with Hanabi, a sixth-grade girl', $item->getSynopsis());
        $this->assertContains('OLM', $item->getProducers()[0]);
    }
}
