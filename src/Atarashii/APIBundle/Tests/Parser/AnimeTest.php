<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Model\Anime;
use Atarashii\APIBundle\Parser\AnimeParser;

/**
 * Class AnimeTest
 * @package Atarashii\APIBundle\Tests\Parser
 * @coversDefaultClass Atarashii\APIBundle\Parser\AnimeParser
 */
class AnimeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::parse
     */
    public function testParse()
    {
        $animeContents = file_get_contents(__DIR__ . '/../../Resources/Samples/Input/anime-1887-mine.htm');

        $anime = AnimeParser::parse($animeContents);

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Anime', $anime);

        /* As this test is against a static downloaded copy, we know what the exact values should be. As such, rather
         * than testing based on type for many of the items, we're checking for exact values. Obviously, this test
         * will need to be updated when the source file is re-downloaded to update any values.
         */

        $this->assertEquals('1887', $anime->getId());
        $this->assertEquals('Lucky☆Star', $anime->getTitle());
        $this->assertEquals('Lucky☆Star', $anime->getOtherTitles()['english'][0]);
        $this->assertEquals('Lucky Star', $anime->getOtherTitles()['synonyms'][0]);
        $this->assertEquals('らき☆すた', $anime->getOtherTitles()['japanese'][0]);
        $this->assertEquals(583, $anime->getRank());
        $this->assertEquals(59, $anime->getPopularityRank());
        $this->assertEquals('http://cdn.myanimelist.net/images/anime/13/15010.jpg', $anime->getImageUrl());
        $this->assertEquals('TV', $anime->getType());
        $this->assertEquals(24, $anime->getEpisodes());
        $this->assertEquals('finished airing', $anime->getStatus());
        $this->assertEquals('2007-04-08', $anime->getStartDate());
        $this->assertEquals('2007-09-17', $anime->getEndDate());
        $this->assertEquals('PG-13 - Teens 13 or older', $anime->getClassification());
        $this->assertEquals(7.95, $anime->getMembersScore());
        $this->assertEquals(239622, $anime->getMembersCount());
        $this->assertEquals(7277, $anime->getFavoritedCount());
        $this->assertStringStartsWith('Having fun in school, doing homework together', $anime->getSynopsis());
        $this->assertContains('Kyoto Animation', $anime->getProducers());
        $this->assertContains('Lantis', $anime->getProducers());
        $this->assertContains('Comedy', $anime->getGenres());
        $this->assertContains('Parody', $anime->getGenres());

        $this->assertInternalType('array', $anime->getMangaAdaptations()[0]);
        $this->assertEquals('587', $anime->getMangaAdaptations()[0]['manga_id']);
        $this->assertEquals('Lucky☆Star', $anime->getMangaAdaptations()[0]['title']);
        $this->assertContains('manga/587', $anime->getMangaAdaptations()[0]['url']);

        $this->assertEmpty($anime->getPrequels());

        $this->assertInternalType('array', $anime->getSequels()[0]);
        $this->assertEquals('4472', $anime->getSequels()[0]['anime_id']);
        $this->assertStringStartsWith('Lucky☆Star', $anime->getSequels()[0]['title']);
        $this->assertContains('anime/4472', $anime->getSequels()[0]['url']);

        $this->assertEmpty($anime->getSideStories());

        $this->assertInternalType('array', $anime->getCharacterAnime()[0]);
        $this->assertEquals('3080', $anime->getCharacterAnime()[0]['anime_id']);
        $this->assertEquals('Anime Tenchou', $anime->getCharacterAnime()[0]['title']);
        $this->assertContains('anime/3080', $anime->getCharacterAnime()[0]['url']);

        $this->assertInternalType('array', $anime->getSpinOffs()[0]);
        $this->assertEquals('17637', $anime->getSpinOffs()[0]['anime_id']);
        $this->assertStringStartsWith('Miyakawa-ke', $anime->getSpinOffs()[0]['title']);
        $this->assertContains('anime/17637', $anime->getSpinOffs()[0]['url']);

        $this->assertEmpty($anime->getSummaries());
        $this->assertEmpty($anime->getAlternativeVersions());
        $this->assertEmpty($anime->getOther());

        $this->assertEquals('completed', $anime->getWatchedStatus('string'));
        $this->assertEquals(24, $anime->getWatchedEpisodes());

        $this->assertEquals(7, $anime->getScore());
    }

    /**
     * @covers ::parseExtendedPersonal
     */
    public function testParseExtendedPersonal()
    {
        $anime = new Anime();

        $animeContents = file_get_contents(__DIR__ . '/../../Resources/Samples/Input/anime-1689-mine-detailed.htm');

        AnimeParser::parseExtendedPersonal($animeContents, $anime);

        $this->assertInternalType('array', $anime->getPersonalTags());
        $this->assertContains('beautiful', $anime->getPersonalTags());
        $this->assertEquals('2013-03-04', $anime->getWatchingStart()->format('Y-m-d'));
        $this->assertEquals('2013-03-04', $anime->getWatchingEnd()->format('Y-m-d'));
        $this->assertEquals('High', $anime->getPriority('string'));
        $this->assertEquals(4, $anime->getStorage());
        $this->assertEquals(1, $anime->getStorageValue());
        $this->assertEquals(3, $anime->getEpsDownloaded());
        $this->assertEquals(0, $anime->getRewatchCount());
        $this->assertEquals('High', $anime->getRewatchValue('string'));
        $this->assertStringStartsWith('The beautiful art direction', $anime->getPersonalComments());



    }
}