<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Parser\CastParser;

class CastTest extends \PHPUnit_Framework_TestCase
{
    public function testParseAnime()
    {
        $castContents = file_get_contents(__DIR__.'/../InputSamples/anime-1887-cast.html');

        $castList = CastParser::parse($castContents);

        $this->assertInternalType('array', $castList);

        $characterList = $castList['Characters'];
        //Kagami
        foreach ($characterList as $characterItem) {
            if ($characterItem->getId() == 2171) {
                $character = $characterItem;
                break;
            }
        }

        $staffList = $castList['Staff'];

        //Yasuhiro Takemoto
        foreach ($staffList as $staffItem) {
            if ($staffItem->getId() == 6771) {
                $staff = $staffItem;
                break;
            }
        }

        $actorList = $characterItem->getActors();

        //Emiri Katou
        foreach ($actorList as $actorItem) {
            if ($actorItem->getId() == 52) {
                $actor = $actorItem;
                break;
            }
        }

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Cast', $character);
        $this->assertInstanceOf('Atarashii\APIBundle\Model\Cast', $staff);
        $this->assertInstanceOf('Atarashii\APIBundle\Model\Actor', $actor);

        //Character Tests
        $this->assertEquals(2171, $character->getId());
        $this->assertEquals('Hiiragi, Kagami', $character->getName());
        $this->assertEquals('Main', $character->getRole());
        $this->assertStringStartsWith('https://myanimelist.cdn-dena.com/images/characters/', $character->getImage());

        //Actor Tests
        $this->assertEquals(52, $actor->getId());
        $this->assertEquals('Katou, Emiri', $actor->getActorName());
        $this->assertStringStartsWith('https://myanimelist.cdn-dena.com/images/voiceactors/', $actor->getActorImage());
        $this->assertEquals('Japanese', $actor->getActorLanguage());

        //Staff Tests
        $this->assertEquals(6771, $staff->getId());
        $this->assertEquals('Takemoto, Yasuhiro', $staff->getName());
        $this->assertContains('Director', $staff->getRank());
        $this->assertStringStartsWith('https://myanimelist.cdn-dena.com/images/voiceactors/', $staff->getImage()); //Note the path is correct. MAL re-uses this for other non-VA staff images.
    }
}
