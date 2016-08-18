<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Parser\HistoryParser;

/**
 * Class UserTest.
 *
 * @coversDefaultClass Atarashii\APIBundle\Parser\HistoryParser
 */
class HistoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::parse
     */
    public function testParse()
    {
        $anime = file_get_contents(__DIR__.'/../InputSamples/anime-1689-history.html');
        $manga = file_get_contents(__DIR__.'/../InputSamples/manga-11977-history.html');

        $animeRes = HistoryParser::parse($anime, 21, 'anime');
        $this->assertNotNull($animeRes);
        $this->assertInternalType('array', $animeRes);

        $historyItem = $animeRes[0];

        $this->assertInstanceOf('Atarashii\ApiBundle\Model\Anime', $historyItem['item']);
        $this->assertEquals('anime', $historyItem['type']);

        $mangaRes = HistoryParser::parse($manga, 21, 'manga');

        $this->assertNotNull($mangaRes);
        $this->assertInternalType('array', $mangaRes);

        $historyItem = $mangaRes[0];

        $this->assertInstanceOf('Atarashii\ApiBundle\Model\Manga', $historyItem['item']);
        $this->assertEquals('manga', $historyItem['type']);
    }
}
