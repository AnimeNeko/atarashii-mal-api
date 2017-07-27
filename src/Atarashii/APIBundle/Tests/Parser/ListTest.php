<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Parser\ListParser;

class ListTest extends \PHPUnit\Framework\TestCase
{
    public function testParse()
    {
        $animeListContents = file_get_contents(__DIR__.'/../InputSamples/anime-list.xml');
        $mangaListContents = file_get_contents(__DIR__.'/../InputSamples/manga-list.xml');

        $animeList = ListParser::parse($animeListContents, 'anime');
        $mangaList = ListParser::parse($mangaListContents, 'manga');

        //Anime List Tests
        foreach ($animeList['anime'] as $listItem) {
            if ($listItem->getId() === 189) {
                $animeItem = $listItem;
                break;
            }
        }

        $this->assertEquals('Love Hina', $animeItem->getTitle());
        $this->assertEquals('completed', $animeItem->getWatchedStatus());
        $this->assertInternalType('int', $animeItem->getScore());
        $this->assertEquals(7, $animeItem->getScore());

        //Manga List Tests
        foreach ($mangaList['manga'] as $listItem) {
            if ($listItem->getId() === 436) {
                $mangaItem = $listItem;
                break;
            }
        }

        $this->assertEquals('Uzumaki', $mangaItem->getTitle());
        $this->assertEquals('completed', $mangaItem->getReadStatus());
        $this->assertInternalType('int', $mangaItem->getScore());
        $this->assertEquals(9, $mangaItem->getScore());
    }
}
