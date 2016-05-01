<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Parser\RecsParser;

class RecsTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $recsContents = file_get_contents(__DIR__.'/../InputSamples/anime-21-recs.html');

        $content = RecsParser::parse($recsContents);

        $this->assertInternalType('array', $content);

        $recsContent = $content[0];

        $this->assertInternalType('array', $recsContent);

        $this->assertEquals(6702, $recsContent['item']->getId());
        $this->assertInternalType('string', $recsContent['item']->getTitle());
        $this->assertInternalType('string', $recsContent['item']->getImageUrl());
        $this->assertInternalType('string', $recsContent['recommendations'][0]['information']);
        $this->assertInternalType('string', $recsContent['recommendations'][0]['username']);

        $recsContents = file_get_contents(__DIR__.'/../InputSamples/manga-21-recs.html');

        $content = RecsParser::parse($recsContents);

        $this->assertInternalType('array', $content);

        $recsContent = $content[0];

        $this->assertInternalType('array', $recsContent);

        $this->assertEquals(1649, $recsContent['item']->getId());
        $this->assertInternalType('string', $recsContent['item']->getTitle());
        $this->assertInternalType('string', $recsContent['item']->getImageUrl());
        $this->assertInternalType('string', $recsContent['recommendations'][0]['information']);
        $this->assertInternalType('string', $recsContent['recommendations'][0]['username']);
    }
}
