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
        $this->assertNotEmpty($schedule['monday']);
        $item = $schedule['monday'][0];

        $this->assertNotNull($item->getId());
        $this->assertInternalType('int', $item->getId());

        $this->assertInternalType('string', $item->getTitle());

        $this->assertContains('myanimelist.cdn-dena.com/images/anime', $item->getImageUrl());

        $this->assertNotEmpty($item->getGenres());

        $this->assertInternalType('float', $item->getMembersScore());

        $this->assertInternalType('int', $item->getMembersCount());

        $this->assertNotNull($item->getSynopsis());
        $this->assertNotEmpty($item->getProducers());
    }
}
