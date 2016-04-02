<?php

namespace Atarashii\APIBundle\Tests\Model;

use Atarashii\APIBundle\Model\Anime;
use Atarashii\APIBundle\Model\Manga;
use Atarashii\APIBundle\Model\Person;

class PersonTest extends \PHPUnit_Framework_TestCase
{
    public function testId()
    {
        $person = new Person();

        $id = rand();
        $person->setId($id);
        $this->assertEquals($id, $person->getId());
    }

    public function testImageUrl()
    {
        $person = new Person();

        $imageUrl = 'http://www.example.com/image.jpg';
        $person->setImageUrl($imageUrl);
        $this->assertEquals($imageUrl, $person->getImageUrl());
    }

    public function testName()
    {
        $person = new Person();

        $name = 'Jones, Henry';
        $person->setName($name);
        $this->assertEquals($name, $person->getName());
    }

    public function testAlternateNames()
    {
        $person = new Person();

        $alternateNames = array('Indiana');

        $person->setAlternateNames($alternateNames);
        $this->assertEquals($alternateNames, $person->getAlternateNames());
    }

    public function testGivenName()
    {
        $person = new Person();

        $givenName = '香菜';
        $person->setGivenName($givenName);
        $this->assertEquals($givenName, $person->getGivenName());
    }

    public function testFamilyName()
    {
        $person = new Person();

        $familyName = '花澤';
        $person->setFamilyName($familyName);
        $this->assertEquals($familyName, $person->getFamilyName());
    }

    public function testBirthday()
    {
        $person = new Person();
        $birthday = new \DateTime('now');

        // Day
        $person->setBirthday($birthday, 'day');
        $verifyDate = $birthday->format('Y-m-d');
        $this->assertEquals($verifyDate, $person->getBirthday());

        // Month
        $person->setBirthday($birthday, 'month');
        $verifyDate = $birthday->format('Y-m');
        $this->assertEquals($verifyDate, $person->getBirthday());

        // Year
        $person->setBirthday($birthday, 'year');
        $verifyDate = $birthday->format('Y');
        $this->assertEquals($verifyDate, $person->getBirthday());
    }

    public function testWebsiteUrl()
    {
        $person = new Person();

        $websiteUrl = 'http://www.example.com/';
        $person->setWebsiteUrl($websiteUrl);
        $this->assertEquals($websiteUrl, $person->getWebsiteUrl());
    }

    public function testMoreDetails()
    {
        $person = new Person();

        $moreDetails = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent porta, lectus vitae scelerisque ornare, mi sem posuere dolor, in feugiat sapien dolor quis ex. Aliquam varius sem at urna viverra, non vulputate mi cursus.';
        $person->setMoreDetails($moreDetails);
        $this->assertEquals($moreDetails, $person->getMoreDetails());

        // Test Unicode
        $moreDetails = '祦覦 窨鎌ば亜䏤 げずキェ祦覦 㦵土みゃ, 尦奚飌䨣みゅ じゃ嶣馺ギェひゅ げずキェ祦覦 訣が榊 亜䏤 秺碜䋥 䯞ぬ果クァ㩟';
        $person->setMoreDetails($moreDetails);
        $this->assertEquals($moreDetails, $person->getMoreDetails());
    }

    public function testFavoritedCount()
    {
        $person = new Person();

        $favoritedCount = 30234;
        $person->setFavoritedCount($favoritedCount);
        $this->assertEquals($favoritedCount, $person->getFavoritedCount());

        // Test bad data
        $favoritedCount = 'Fake';
        $person->setFavoritedCount($favoritedCount);
        $this->assertEquals($favoritedCount, $person->getFavoritedCount());
    }

    public function testVoiceActingRoles()
    {
        $person = new Person();

        $itemArray = array(
            'id' => rand(),
            'name' => 'Sengoku, Nadeko',
            'image_url' => 'http://www.example.com/image.jpg',
            'main_role' => true
        );

        $itemArray['anime'] = new Anime();
        $itemArray['anime']->setId(rand());
        $itemArray['anime']->setTitle('The Title');
        $itemArray['anime']->setImageUrl('http://www.example.com/image.jpg');

        $voiceActingRoles = array($itemArray);
        $person->setVoiceActingRoles($voiceActingRoles);
        $this->assertEquals($voiceActingRoles, $person->getVoiceActingRoles());
    }

    public function testAnimeStaffPositions()
    {
        $person = new Person();

        $itemArray = array(
            'position' => 'Opening Theme',
            'details' => 'OP 1, 2, 3'
        );

        $itemArray['anime'] = new Anime();
        $itemArray['anime']->setId(rand());
        $itemArray['anime']->setTitle('The Title');
        $itemArray['anime']->setImageUrl('http://www.example.com/image.jpg');

        $animeStaffPositions = array($itemArray);
        $person->setAnimeStaffPositions($animeStaffPositions);
        $this->assertEquals($animeStaffPositions, $person->getAnimeStaffPositions());
    }

    public function testPublishedManga()
    {
        $person = new Person();

        $itemArray = array(
            'position' => 'Story'
        );

        $itemArray['manga'] = new Manga();
        $itemArray['manga']->setId(rand());
        $itemArray['manga']->setTitle('The Title');
        $itemArray['manga']->setImageUrl('http://www.example.com/image.jpg');

        $publishedManga = array($itemArray);

        $person->setPublishedManga($publishedManga);
        $this->assertEquals($publishedManga, $person->getPublishedManga());
    }

}
