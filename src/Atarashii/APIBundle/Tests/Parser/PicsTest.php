<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Parser\TitleParser;

class PicsTest extends \PHPUnit\Framework\TestCase
{
    public function testParse()
    {
        $animePicsContents = file_get_contents(__DIR__.'/../InputSamples/anime-1887-pics.html');
        $mangaPicsContents = file_get_contents(__DIR__.'/../InputSamples/manga-11977-pics.html');
        $apiVersion = '2.2';

        $animePics = TitleParser::parsePics($animePicsContents, 'anime');
        $mangaPics = TitleParser::parsePics($mangaPicsContents, 'manga');

        $this->assertInternalType('array', $animePics);
        $this->assertInternalType('array', $mangaPics);

        $animePicture = $animePics[0];
        $mangaPicture = $mangaPics[0];

        $this->assertContains('myanimelist.cdn-dena.com/images/anime', $animePicture);
        $this->assertContains('myanimelist.cdn-dena.com/images/manga', $mangaPicture);
    }
}
