<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Model\Manga;
use Atarashii\APIBundle\Parser\MangaParser;

/**
 * Class MangaTest
 * @package Atarashii\APIBundle\Tests\Parser
 * @coversDefaultClass Atarashii\APIBundle\Parser\MangaParser
 */
class MangaTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::parse
     */
    public function testParse()
    {
        $mangaContents = file_get_contents(__DIR__ . '/../../Resources/Samples/Input/manga-11977-mine.html');

        $manga = MangaParser::parse($mangaContents);

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Manga', $manga);

        /* As this test is against a static downloaded copy, we know what the exact values should be. As such, rather
         * than testing based on type for many of the items, we're checking for exact values. Obviously, this test
         * will need to be updated when the source file is re-downloaded to update any values.
         */

        $this->assertEquals(11977, $manga->getId());
        $this->assertEquals('Bambino!', $manga->getTitle());

        $this->assertArrayHasKey('japanese', $manga->getOtherTitles());
        $this->assertContains('バンビ～ノ！', $manga->getOtherTitles()['japanese']);

        $this->assertInternalType('integer', $manga->getRank());
        $this->assertLessThan(850, $manga->getRank());
        $this->assertGreaterThan(0, $manga->getRank());


        $this->assertInternalType('integer', $manga->getPopularityRank());
        $this->assertLessThan(2500, $manga->getPopularityRank());
        $this->assertGreaterThan(0, $manga->getPopularityRank());

        $this->assertEquals('http://cdn.myanimelist.net/images/manga/5/74977.jpg', $manga->getImageUrl());
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

        $this->assertInternalType('array', $manga->getRelatedManga()[0]);
        $this->assertEquals('19008', $manga->getRelatedManga()[0]['manga_id']);
        $this->assertStringStartsWith('Bambino! Secondo', $manga->getRelatedManga()[0]['title']);
        $this->assertContains('manga/19008', $manga->getRelatedManga()[0]['url']);

        $this->assertEmpty($manga->getAlternativeVersions());

        $this->assertEquals('completed', $manga->getReadStatus('string'));
        $this->assertEquals(164, $manga->getChaptersRead());
        $this->assertEquals(15, $manga->getVolumesRead());
        $this->assertEquals(7, $manga->getScore());

        $mangaContents = file_get_contents(__DIR__ . '/../../Resources/Samples/Input/manga-137-mine.html');

        $manga = MangaParser::parse($mangaContents);

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Manga', $manga);

        $this->assertArrayHasKey('english', $manga->getOtherTitles());
        $this->assertContains('Read or Die', $manga->getOtherTitles()['english']);

        $this->assertArrayHasKey('synonyms', $manga->getOtherTitles());
        $this->assertContains('R.O.D.', $manga->getOtherTitles()['synonyms']);

        $this->assertInternalType('array', $manga->getAnimeAdaptations()[0]);
        $this->assertEquals('208', $manga->getAnimeAdaptations()[0]['anime_id']);
        $this->assertStringStartsWith('R.O.D OVA', $manga->getAnimeAdaptations()[0]['title']);
        $this->assertContains('anime/208', $manga->getAnimeAdaptations()[0]['url']);

        $this->assertInternalType('array', $manga->getAlternativeVersions()[0]);
        $this->assertEquals('10869', $manga->getAlternativeVersions()[0]['manga_id']);
        $this->assertStringStartsWith('Read or Die', $manga->getAlternativeVersions()[0]['title']);
        $this->assertContains('manga/10869', $manga->getAlternativeVersions()[0]['url']);

        $mangaContents = file_get_contents(__DIR__ . '/../../Resources/Samples/Input/manga-44347.html');

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

        $mangaContents = file_get_contents(__DIR__ . '/../../Resources/Samples/Input/manga-11977-mine-detailed.html');

        MangaParser::parseExtendedPersonal($mangaContents, $manga);

        $this->assertContains('drama', $manga->getPersonalTags());
        $this->assertContains('seinen', $manga->getPersonalTags());
        $this->assertEquals('2013-09-25', $manga->getReadingStart()->format('Y-m-d'));
        $this->assertEquals('2013-10-11', $manga->getReadingEnd()->format('Y-m-d'));
        $this->assertEquals('Low', $manga->getPriority('string'));
        $this->assertEquals(164, $manga->getChapDownloaded());
        $this->assertEquals('Medium', $manga->getRereadValue('string'));
        $this->assertStringStartsWith('An interesting spin', $manga->getPersonalComments());
    }
}