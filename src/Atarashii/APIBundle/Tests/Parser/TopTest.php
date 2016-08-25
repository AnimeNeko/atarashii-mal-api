<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Parser\Top;

class TopTest extends \PHPUnit_Framework_TestCase
{
    public function testParseAnime()
    {
        $animeContents = file_get_contents(__DIR__.'/../InputSamples/anime-top.html');

        $topList = Top::parse($animeContents, 'anime');

        $this->assertInternalType('array', $topList);

        $topItem = $topList[0];

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Anime', $topItem);

        /* Although this test is against a static downloaded copy, the values are likely to change considerably
         * each time the source file is updated. As such, mostly check the type and confirm a few things that
         * shouldn't change.
         */

        //Do some basic type checking for things that should exist (not null)
        $this->assertInternalType('int', $topItem->getId());
        $this->assertInternalType('string', $topItem->getTitle());
        $this->assertInternalType('string', $topItem->getImageUrl());
        $this->assertInternalType('string', $topItem->getType());
        $this->assertInternalType('float', $topItem->getMembersScore());
        $this->assertInternalType('int', $topItem->getMembersCount());

        //Check some data that should remain consistent
        $this->assertStringStartsWith('https://myanimelist.cdn-dena.com/images/anime/', $topItem->getImageUrl());
        $this->assertInstanceOf('\DateTime', new \DateTime($topItem->getStartDate()));
        $this->assertGreaterThan(8, $topItem->getMembersScore());
        $this->assertGreaterThan(60000, $topItem->getMembersCount());
    }

    public function testParseManga()
    {
        $mangaContents = file_get_contents(__DIR__.'/../InputSamples/manga-top.html');

        $topList = Top::parse($mangaContents, 'manga');

        $this->assertInternalType('array', $topList);

        $topItem = $topList[0];

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Manga', $topItem);

        /* Although this test is against a static downloaded copy, the values are likely to change considerably
         * each time the source file is updated. As such, mostly check the type and confirm a few things that
         * shouldn't change.
         */

        //Do some basic type checking for things that should exist (not null)
        $this->assertInternalType('int', $topItem->getId());
        $this->assertInternalType('string', $topItem->getTitle());
        $this->assertInternalType('string', $topItem->getImageUrl());
        $this->assertInternalType('float', $topItem->getMembersScore());
        $this->assertInternalType('int', $topItem->getMembersCount());

        //Check some data that should remain consistent
        $this->assertStringStartsWith('https://myanimelist.cdn-dena.com/images/manga/', $topItem->getImageUrl());
        $this->assertGreaterThan(8, $topItem->getMembersScore());
        $this->assertGreaterThan(50000, $topItem->getMembersCount());
    }
}
