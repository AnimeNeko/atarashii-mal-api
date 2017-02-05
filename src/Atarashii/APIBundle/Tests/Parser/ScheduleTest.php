<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Parser\ScheduleParser;

/**
 * Class ScheduleTest.
 *
 * @coversDefaultClass Atarashii\APIBundle\Parser\ScheduleParser
 */
class ScheduleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers ::parse
     */
    public function testParse()
    {
        $contents = file_get_contents(__DIR__.'/../InputSamples/schedule-26052016.html');

        $schedule = ScheduleParser::parse($contents);

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
