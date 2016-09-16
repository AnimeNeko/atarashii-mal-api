<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Model\Anime;
use Atarashii\APIBundle\Parser\AnimeParser;

/**
 * Class AnimeTest.
 *
 * @coversDefaultClass Atarashii\APIBundle\Parser\AnimeParser
 */
class AnimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::parse
     */
    public function testParse()
    {
        $animeContents = file_get_contents(__DIR__.'/../InputSamples/anime-1887-mine.html');
        $apiVersion = '2.1';

        $anime = AnimeParser::parse($animeContents, $apiVersion);

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Anime', $anime);

        /* As this test is against a static downloaded copy, we know what the exact values should be. As such, rather
         * than testing based on type for many of the items, we're checking for exact values. Obviously, this test
         * will need to be updated when the source file is re-downloaded to update any values.
         */

        $this->assertEquals('1887', $anime->getId());
        $this->assertEquals('Lucky☆Star', $anime->getTitle());
        $this->assertContains('youtube.com/embed/b8bSIgE8epA', $anime->getPreview());

        $oTitles = $anime->getOtherTitles();

        $this->assertArrayHasKey('english', $oTitles);
        $this->assertContains('Lucky☆Star', $oTitles['english']);

        $this->assertArrayHasKey('synonyms', $oTitles);
        $this->assertContains('Lucky Star', $oTitles['synonyms']);

        $this->assertArrayHasKey('japanese', $oTitles);
        $this->assertContains('らき☆すた', $oTitles['japanese']);

        $this->assertInternalType('integer', $anime->getRank());
        $this->assertLessThan(800, $anime->getRank());
        $this->assertGreaterThan(0, $anime->getRank());

        $this->assertInternalType('integer', $anime->getPopularityRank());
        $this->assertLessThan(100, $anime->getPopularityRank());
        $this->assertGreaterThan(0, $anime->getPopularityRank());

        $this->assertEquals('https://myanimelist.cdn-dena.com/images/anime/13/15010.jpg', $anime->getImageUrl());
        $this->assertEquals('TV', $anime->getType());
        $this->assertEquals(24, $anime->getEpisodes());
        $this->assertEquals('finished airing', $anime->getStatus());
        $this->assertEquals('2007-04-08', $anime->getStartDate());
        $this->assertEquals('2007-09-17', $anime->getEndDate());
        $this->assertEquals(24, $anime->getDuration());
        $this->assertEquals(null, $anime->getBroadcast());
        $this->assertEquals(24, $anime->getDuration());
        $this->assertEquals('PG-13 - Teens 13 or older', $anime->getClassification());

        $this->assertInternalType('float', $anime->getMembersScore());
        $this->assertGreaterThan(6.0, $anime->getMembersScore());

        $this->assertGreaterThan(230000, $anime->getMembersCount());
        $this->assertGreaterThan(7200, $anime->getFavoritedCount());

        $externalLinks = $anime->getExternalLinks();
        $this->assertEquals('http://www.lucky-ch.com/', $externalLinks['Official Site']);
        $this->assertEquals('http://anidb.info/perl-bin/animedb.pl?show=anime&aid=4777', $externalLinks['AnimeDB']);
        $this->assertEquals('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=7222', $externalLinks['AnimeNewsNetwork']);
        $this->assertEquals('http://en.wikipedia.org/wiki/Lucky_Star_%28manga%29', $externalLinks['Wikipedia']);


        $this->assertContains('the daily lives of four cute high school girls', $anime->getSynopsis());
        $this->assertStringStartsWith('<i>Lucky Star</i> also has audio CDs', $anime->getBackground());
        $this->assertContains('Lucky Paradise', $anime->getProducers());
        $this->assertContains('Lantis', $anime->getProducers());
        $this->assertContains('Comedy', $anime->getGenres());
        $this->assertContains('Parody', $anime->getGenres());

        $mangaAdaptations = $anime->getMangaAdaptations();
        $this->assertInternalType('array', $mangaAdaptations[0]);
        $this->assertEquals('587', $mangaAdaptations[0]['manga_id']);
        $this->assertEquals('Lucky☆Star', $mangaAdaptations[0]['title']);
        $this->assertContains('manga/587', $mangaAdaptations[0]['url']);

        $this->assertEmpty($anime->getPrequels());

        $sequels = $anime->getSequels();
        $this->assertInternalType('array', $sequels[0]);
        $this->assertEquals('4472', $sequels[0]['anime_id']);
        $this->assertStringStartsWith('Lucky☆Star', $sequels[0]['title']);
        $this->assertContains('anime/4472', $sequels[0]['url']);

        $this->assertEmpty($anime->getSideStories());

        $charAnime = $anime->getCharacterAnime();
        $this->assertInternalType('array', $charAnime[0]);
        $this->assertEquals('3080', $charAnime[0]['anime_id']);
        $this->assertEquals('Anime Tenchou', $charAnime[0]['title']);
        $this->assertContains('anime/3080', $charAnime[0]['url']);

        $spinOffs = $anime->getSpinOffs();
        $this->assertInternalType('array', $spinOffs[0]);
        $this->assertEquals('17637', $spinOffs[0]['anime_id']);
        $this->assertStringStartsWith('Miyakawa-ke', $spinOffs[0]['title']);
        $this->assertContains('anime/17637', $spinOffs[0]['url']);

        $this->assertEmpty($anime->getSummaries());
        $this->assertEmpty($anime->getAlternativeVersions());
        $this->assertEmpty($anime->getOther());

        $openingTheme = $anime->getOpeningTheme();
        $this->assertEquals('"Motteke! Sailor Fuku" by Aya Hirano, Emiri Katou, Kaori Fukuhara & Aya Endo', $openingTheme[0]);

        $endingTheme = $anime->getEndingTheme();
        $this->assertEquals('#01: "Uchuu Tetsujin Kyoodain (宇宙鉄人キョーダイン)" by Aya Hirano (ep 1)', $endingTheme[0]);
        $this->assertEquals('#23: "Mikuru Henshin! Soshite Sentou! (ミクル変身!そして戦闘!)" by Minoru Shiraishi (ep 23)', $endingTheme[23]);

        $recommedations = $anime->getRecommendations();
        $recommedations = $recommedations[0];
        $this->assertEquals(66, $recommedations->getId());
        $this->assertEquals('Azumanga Daioh', $recommedations->getTitle());
        $this->assertEquals('https://myanimelist.cdn-dena.com/images/anime/1/66.jpg', $recommedations->getImageUrl());

        $animeContents = file_get_contents(__DIR__.'/../InputSamples/anime-10236.html');

        $anime = AnimeParser::parse($animeContents, $apiVersion);

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Anime', $anime);

        $this->assertEquals('1981', $anime->getEndDate());


        $animeContents = file_get_contents(__DIR__.'/../InputSamples/anime-47.html');

        $anime = AnimeParser::parse($animeContents, $apiVersion);

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Anime', $anime);

        $this->assertEquals('124', $anime->getDuration());


        $animeContents = file_get_contents(__DIR__.'/../InputSamples/anime-10758.html');

        $anime = AnimeParser::parse($animeContents, $apiVersion);

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Anime', $anime);

        $this->assertNull($anime->getDuration());


        $animeContents = file_get_contents(__DIR__.'/../InputSamples/anime-18617.html');

        $anime = AnimeParser::parse($animeContents, $apiVersion);

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Anime', $anime);

        $this->assertEquals('120', $anime->getDuration());



    }

    /**
     * @covers ::parseExtendedPersonal
     */
    public function testParseExtendedPersonal()
    {
        $anime = new Anime();

        $animeContents = file_get_contents(__DIR__.'/../InputSamples/anime-1689-mine-detailed.html');

        AnimeParser::parseExtendedPersonal($animeContents, $anime);

        $this->assertInternalType('array', $anime->getPersonalTags());
        $this->assertContains('beautiful', $anime->getPersonalTags());
        $this->assertEquals('2013-03-04', $anime->getWatchingStart()->format('Y-m-d'));
        $this->assertEquals('2013-03-04', $anime->getWatchingEnd()->format('Y-m-d'));
        $this->assertEquals('High', $anime->getPriority('string'));
        $this->assertEquals(4, $anime->getStorage());
        $this->assertEquals(1, $anime->getStorageValue());
        $this->assertEquals(0, $anime->getRewatchCount());
        $this->assertEquals('High', $anime->getRewatchValue('string'));
        $this->assertStringStartsWith('The beautiful art direction', $anime->getPersonalComments());
    }
}
