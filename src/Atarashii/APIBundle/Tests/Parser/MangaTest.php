<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Model\Manga;
use Atarashii\APIBundle\Parser\MangaParser;

/**
 * Class MangaTest.
 *
 * @coversDefaultClass Atarashii\APIBundle\Parser\MangaParser
 */
class MangaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::parse
     */
    public function testParse()
    {
        $mangaContents = file_get_contents(__DIR__.'/../InputSamples/manga-11977-mine.html');

        $manga = MangaParser::parse($mangaContents);

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Manga', $manga);

        /* As this test is against a static downloaded copy, we know what the exact values should be. As such, rather
         * than testing based on type for many of the items, we're checking for exact values. Obviously, this test
         * will need to be updated when the source file is re-downloaded to update any values.
         */

        $this->assertEquals(11977, $manga->getId());
        $this->assertEquals('Bambino!', $manga->getTitle());

        $otherTitles = $manga->getOtherTitles();
        $this->assertArrayHasKey('japanese', $otherTitles);
        $this->assertContains('バンビ～ノ！', $otherTitles['japanese']);

        $this->assertInternalType('integer', $manga->getRank());
        $this->assertLessThan(1200, $manga->getRank());
        $this->assertGreaterThan(0, $manga->getRank());

        $this->assertInternalType('integer', $manga->getPopularityRank());
        $this->assertLessThan(2500, $manga->getPopularityRank());
        $this->assertGreaterThan(0, $manga->getPopularityRank());

        $this->assertEquals('https://myanimelist.cdn-dena.com/images/manga/5/74977.jpg', $manga->getImageUrl());
        $this->assertEquals('Manga', $manga->getType());
        $this->assertEquals(164, $manga->getChapters());
        $this->assertEquals(15, $manga->getVolumes());
        $this->assertEquals('finished', $manga->getstatus());

        $this->assertInternalType('float', $manga->getMembersScore());
        $this->assertGreaterThan(7.0, $manga->getMembersScore());

        $this->assertGreaterThan(2250, $manga->getMembersCount());
        $this->assertGreaterThan(30, $manga->getFavoritedCount());

        $this->assertStringStartsWith('Shogo Ban, a college student from Fukuoka', $manga->getSynopsis());

        $this->assertContains('Drama', $manga->getGenres());
        $this->assertContains('Seinen', $manga->getGenres());

        $this->assertEmpty($manga->getTags());
        $this->assertEmpty($manga->getAnimeAdaptations());

        $relatedManga = $manga->getRelatedManga();
        $this->assertInternalType('array', $relatedManga[0]);
        $this->assertEquals('19008', $relatedManga[0]['manga_id']);
        $this->assertStringStartsWith('Bambino! Secondo', $relatedManga[0]['title']);
        $this->assertContains('manga/19008', $relatedManga[0]['url']);

        $this->assertEmpty($manga->getAlternativeVersions());

        $this->assertEquals('completed', $manga->getReadStatus('string'));
        $this->assertEquals(164, $manga->getChaptersRead());
        $this->assertEquals(15, $manga->getVolumesRead());
        $this->assertEquals(7, $manga->getScore());

        $mangaContents = file_get_contents(__DIR__.'/../InputSamples/manga-137.html');

        $manga = MangaParser::parse($mangaContents);

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Manga', $manga);

        $otherTitles = $manga->getOtherTitles();
        $this->assertArrayHasKey('english', $otherTitles);
        $this->assertContains('Read or Die', $otherTitles['english']);

        $this->assertArrayHasKey('synonyms', $otherTitles);
        $this->assertContains('RoD', $otherTitles['synonyms']);

        $animeAdaptations = $manga->getAnimeAdaptations();
        $this->assertInternalType('array', $animeAdaptations[0]);
        $this->assertEquals('208', $animeAdaptations[0]['anime_id']);
        $this->assertStringStartsWith('R.O.D OVA', $animeAdaptations[0]['title']);
        $this->assertContains('anime/208', $animeAdaptations[0]['url']);

        $mangaContents = file_get_contents(__DIR__.'/../InputSamples/manga-44347.html');

        $manga = MangaParser::parse($mangaContents);

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Manga', $manga);

        $this->assertNull($manga->getChapters());
        $this->assertNull($manga->getVolumes());
    }

    /**
     * @covers ::parseExtendedPersonal
     */
    public function testParseExtendedPersonal()
    {
        $manga = new Manga();

        $mangaContents = file_get_contents(__DIR__.'/../InputSamples/manga-11977-mine-detailed.html');

        MangaParser::parseExtendedPersonal($mangaContents, $manga);

        $this->assertContains('drama', $manga->getPersonalTags());
        $this->assertContains('seinen', $manga->getPersonalTags());
        $this->assertEquals('2013-09-25', $manga->getReadingStart()->format('Y-m-d'));
        $this->assertEquals('2013-10-11', $manga->getReadingEnd()->format('Y-m-d'));
        $this->assertEquals('Low', $manga->getPriority('string'));
        $this->assertEquals('Medium', $manga->getRereadValue('string'));
        $this->assertStringStartsWith('An interesting spin', $manga->getPersonalComments());

        $mangaContents = file_get_contents(__DIR__.'/../InputSamples/manga-17074-mine-detailed.html');

        MangaParser::parseExtendedPersonal($mangaContents, $manga);

        $this->assertEquals('Medium', $manga->getPriority('string'));
        $this->assertEquals(3, $manga->getRereadValue());
        $this->assertEquals(1, $manga->getRereadCount());
    }
}
