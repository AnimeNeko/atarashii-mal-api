<?php

namespace Atarashii\APIBundle\Tests\Model;

use Atarashii\APIBundle\Model\Actor;

class ActorTest extends \PHPUnit_Framework_TestCase
{
    public function testId()
    {
        $actId = rand();

        $actor = new Actor();
        $actor->setId($actId);

        $this->assertEquals($actId, $actor->getId());
    }

    public function testImage()
    {
        $actImgUrl = 'http://www.example.com/image.jpg';

        $actor = new Actor();
        $actor->setImage($actImgUrl);

        $this->assertEquals($actImgUrl, $actor->getActorImage());
    }

    public function testLanguage()
    {
        $actLang = 'Japanese';

        $actor = new Actor();
        $actor->setLanguage($actLang);

        $this->assertEquals($actLang, $actor->getActorLanguage());
    }

    public function testName()
    {
        $actName = 'Taro Yamada';

        $actor = new Actor();
        $actor->setName($actName);

        $this->assertEquals($actName, $actor->getActorName());
    }
}
