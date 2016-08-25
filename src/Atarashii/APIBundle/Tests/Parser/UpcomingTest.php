<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Parser\Upcoming;

class UpcomingTest extends \PHPUnit_Framework_TestCase
{
    public function testParseAnime()
    {
        $animeContents = file_get_contents(__DIR__.'/../InputSamples/anime-upcoming.html');

        $upcomingList = Upcoming::parse($animeContents, 'anime');

        $this->assertInternalType('array', $upcomingList);

        $upcomingItem = $upcomingList[0];

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Anime', $upcomingItem);

        /* Although this test is against a static downloaded copy, the values are likely to change considerably
         * each time the source file is updated. As such, mostly check the type and confirm a few things that
         * shouldn't change.
         */

        //Do some basic type checking for things that should exist (not null)
        $this->assertInternalType('int', $upcomingItem->getId());
        $this->assertInternalType('string', $upcomingItem->getTitle());
        $this->assertInternalType('string', $upcomingItem->getImageUrl());
        $this->assertInternalType('string', $upcomingItem->getType());
        $this->assertInternalType('string', $upcomingItem->getSynopsis());
        $this->assertInternalType('string', $upcomingItem->getStartDate());

        //Check some data that should remain consistent
        $this->assertStringStartsWith('https://myanimelist.cdn-dena.com/images/anime/', $upcomingItem->getImageUrl());
        $this->assertInstanceOf('\DateTime', new \DateTime($upcomingItem->getStartDate()));

        // Get some older titles to test date parsing and wrapping for two-digit years
        $animeContents = file_get_contents(__DIR__.'/../InputSamples/anime-upcoming-1930.html');

        $upcomingList = Upcoming::parse($animeContents, 'anime');

        $this->assertInternalType('array', $upcomingList);

        $upcomingItem = $upcomingList[0];

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Anime', $upcomingItem);
        $this->assertStringStartsWith('1930', $upcomingItem->getStartDate());
    }

    public function testParseManga()
    {
        $mangaContents = file_get_contents(__DIR__.'/../InputSamples/manga-upcoming.html');

        $upcomingList = Upcoming::parse($mangaContents, 'manga');

        $this->assertInternalType('array', $upcomingList);

        $upcomingItem = $upcomingList[0];

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Manga', $upcomingItem);

        /* Although this test is against a static downloaded copy, the values are likely to change considerably
         * each time the source file is updated. As such, mostly check the type and confirm a few things that
         * shouldn't change.
         */

        //Do some basic type checking for things that should exist (not null)
        $this->assertInternalType('int', $upcomingItem->getId());
        $this->assertInternalType('string', $upcomingItem->getTitle());
        $this->assertInternalType('string', $upcomingItem->getImageUrl());
        $this->assertInternalType('string', $upcomingItem->getType());
        $this->assertInternalType('string', $upcomingItem->getSynopsis());

        //Check some data that should remain consistent
        $this->assertStringStartsWith('https://myanimelist.cdn-dena.com/images/manga/', $upcomingItem->getImageUrl());
    }
}
