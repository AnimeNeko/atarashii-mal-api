<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Model\Person;
use Atarashii\APIBundle\Parser\PersonParser;

class PersonTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        // Test Kana Hanazawa (a "normal" profile)
        $personContents = file_get_contents(__DIR__.'/../InputSamples/people-185.html');
        $person = PersonParser::parse($personContents);
        $this->assertInstanceOf('Atarashii\APIBundle\Model\Person', $person);

        $this->assertEquals('185', $person->getId());
        $this->assertEquals('https://myanimelist.cdn-dena.com/images/voiceactors/3/43500.jpg', $person->getImageUrl());
        $this->assertEquals('Hanazawa, Kana', $person->getName());
        $this->assertEquals('香菜', $person->getGivenName());
        $this->assertEquals('花澤', $person->getFamilyName());
        $this->assertEquals('1989-02-25', $person->getBirthday());
        $this->assertEquals('http://www.hanazawakana-music.net/', $person->getWebsiteUrl());
        $this->assertGreaterThan('30000', $person->getFavoritedCount());
        $this->assertContains('Height: 156 cm', $person->getMoreDetails());

        $this->assertGreaterThan(250, count($person->getVoiceActingRoles()));
        $this->assertGreaterThanOrEqual(30, count($person->getAnimeStaffPositions()));


        // Test Miyazaki Hayao ("normal" with published manga)
        $personContents = file_get_contents(__DIR__.'/../InputSamples/people-1870.html');
        $person = PersonParser::parse($personContents);
        $this->assertInstanceOf('Atarashii\APIBundle\Model\Person', $person);

        $this->assertGreaterThan(10, count($person->getPublishedManga()));


        // Test Johnny Yong Bosch ("normal" with alternate names)
        $personContents = file_get_contents(__DIR__.'/../InputSamples/people-10.html');
        $person = PersonParser::parse($personContents);
        $this->assertInstanceOf('Atarashii\APIBundle\Model\Person', $person);

        $this->assertGreaterThanOrEqual(1, count($person->getAlternateNames()));


        // Test huke (interesting case for family name parsing)
        $personContents = file_get_contents(__DIR__.'/../InputSamples/people-10145.html');
        $person = PersonParser::parse($personContents);
        $this->assertInstanceOf('Atarashii\APIBundle\Model\Person', $person);

        $this->assertEquals('huke', $person->getFamilyName());
        $this->assertEquals('', $person->getGivenName());


        // Test Ikimono-gakari (birthday, year & month only)
        $personContents = file_get_contents(__DIR__.'/../InputSamples/people-7277.html');
        $person = PersonParser::parse($personContents);
        $this->assertInstanceOf('Atarashii\APIBundle\Model\Person', $person);

        $this->assertEquals('1999-02', $person->getBirthday());


        // Test ClairS (birthday, year only)
        $personContents = file_get_contents(__DIR__.'/../InputSamples/people-11746.html');
        $person = PersonParser::parse($personContents);
        $this->assertInstanceOf('Atarashii\APIBundle\Model\Person', $person);

        $this->assertEquals('2009', $person->getBirthday());


        // Test Miyamoto Kano (birthday, month and day only)
        $personContents = file_get_contents(__DIR__.'/../InputSamples/people-2608.html');
        $person = PersonParser::parse($personContents);
        $this->assertInstanceOf('Atarashii\APIBundle\Model\Person', $person);

        $this->assertEquals('-06-15', $person->getBirthday());
    }
}
