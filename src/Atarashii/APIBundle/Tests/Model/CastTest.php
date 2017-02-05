<?php

namespace Atarashii\APIBundle\Tests\Model;

use Atarashii\APIBundle\Model\Actor;
use Atarashii\APIBundle\Model\Cast;

class CastTest extends \PHPUnit\Framework\TestCase
{
    public function testId()
    {
        $castId = rand();

        $cast = new Cast();
        $cast->setId($castId);

        $this->assertEquals($castId, $cast->getId());
    }

    public function testName()
    {
        $castName = 'Taro Yamada';

        $cast = new Cast();
        $cast->setName($castName);

        $this->assertEquals($castName, $cast->getName());
    }

    public function testRole()
    {
        $castRole = 'Main';

        $cast = new Cast();
        $cast->setRole($castRole);

        $this->assertEquals($castRole, $cast->getRole());
    }

    public function testImage()
    {
        $castImgUrl = 'http://www.example.com/image.jpg';

        $cast = new Cast();
        $cast->setImage($castImgUrl);

        $this->assertEquals($castImgUrl, $cast->getImage());

        //Unknown Image
        $unknownImage = 'https://myanimelist.cdn-dena.com/images/questionmark_23.gif';
        $expectedImage = 'https://myanimelist.cdn-dena.com/images/na.gif';

        $cast->setImage($unknownImage);

        $this->assertEquals($expectedImage, $cast->getImage());
    }

    public function testRank()
    {
        $castRank = 'Director';

        $cast = new Cast();
        $cast->setRank($castRank);

        $this->assertEquals($castRank, $cast->getRank());
    }

    public function testActors()
    {
        $actor = new Actor();
        $actor->setId(1);
        $actor->setName('Taro Yamada');
        $actor->setLanguage('Japanese');
        $actor->setImage('http://www.example.com/image.jpg');

        $cast = new Cast();
        $cast->setActors($actor);

        $actors = $cast->getActors();

        $this->assertEquals($actor, $actors[0]);
    }
}
