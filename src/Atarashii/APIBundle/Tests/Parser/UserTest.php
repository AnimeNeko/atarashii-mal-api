<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Parser\User;

/**
 * Class UserTest.
 *
 * @coversDefaultClass Atarashii\APIBundle\Parser\User
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    private $profile;
    private $profile2;

    /**
     * @covers ::parse
     */
    public function testParse()
    {
        $profile = $this->profile;

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Profile', $profile);
        $this->assertInstanceOf('Atarashii\APIBundle\Model\ProfileDetails', $profile->details);
        $this->assertInstanceOf('Atarashii\APIBundle\Model\AnimeStats', $profile->anime_stats);
        $this->assertInstanceOf('Atarashii\APIBundle\Model\MangaStats', $profile->manga_stats);

        /* As this test is against a static downloaded copy, we know what the exact values should be. As such, rather
         * than testing based on type for many of the items, we're checking for exact values. Obviously, this test
         * will need to be updated when the source file is re-downloaded to update any values.
         */

        $this->assertEquals('https://myanimelist.cdn-dena.com/images/userimages/1183995.jpg', $profile->getAvatarUrl());
    }

    /**
     * @covers ::parseDetails
     */
    public function testParseDetails()
    {
        // Profile Details
        $profileDetails = $this->profile->details;

        //Source will vary, so store the expected values based on the source.
        $expectedString = '3 hours ago';

        //MAL defaults to this timezone for general dates when not logged in.
        $expected = new \DateTime();
        $expected->setTimezone(new \DateTimeZone('America/Los_Angeles'));
        $expected->modify('-3 hours');
        $expectedTimeString = $expected->format('Y-m-d\THO'); // Full ISO-8601 is Y-m-d\TH:iO

        $this->assertEquals($expectedString, $profileDetails->getLastOnline());
        $this->assertEquals($expectedTimeString, $profileDetails->getLastOnline2());
        $this->assertNull($profileDetails->getStatus());
        $this->assertEquals('Not specified', $profileDetails->getGender());
        $this->assertEquals('April 28, 1981', $profileDetails->getBirthday());
        $this->assertEquals('California, USA', $profileDetails->getLocation());
        $this->assertEquals('http://www.animeneko.net', $profileDetails->getWebsite());
        $this->assertEquals('March 5, 2012', $profileDetails->getJoinDate());
        $this->assertEquals('2012-03-05', $profileDetails->getJoinDate2());
        $this->assertEquals('Member', $profileDetails->getAccessRank());
        $this->assertGreaterThanOrEqual(57, $profileDetails->getForumPosts());
        $this->assertNull($profileDetails->getAim());
        $this->assertNull($profileDetails->getMsn());
        $this->assertNull($profileDetails->getYahoo());

        //Some extra tests based on profile #2 (a privileged account)
        $profileDetails = $this->profile2->details;

        $this->assertEquals('Site Administrator', $profileDetails->getAccessRank());
        $this->assertEquals('Male', $profileDetails->getGender());
    }

    /**
     * @covers ::parseStats
     */
    public function testParseStats()
    {
        // Profile Details
        $animeStats = $this->profile->anime_stats;
        $mangaStats = $this->profile->manga_stats;

        //Anime Stats
        $this->assertGreaterThanOrEqual(58.2, $animeStats->getTimeDays());
        $this->assertGreaterThanOrEqual(13, $animeStats->getWatching());
        $this->assertGreaterThanOrEqual(247, $animeStats->getCompleted());
        $this->assertGreaterThanOrEqual(20, $animeStats->getOnHold());
        $this->assertGreaterThanOrEqual(3, $animeStats->getDropped());
        $this->assertGreaterThanOrEqual(62, $animeStats->getPlanToWatch());
        $this->assertGreaterThanOrEqual(345, $animeStats->getTotalEntries());

        //Manga Stats
        $this->assertGreaterThanOrEqual(22, $mangaStats->getTimeDays());
        $this->assertGreaterThanOrEqual(22, $mangaStats->getReading());
        $this->assertGreaterThanOrEqual(83, $mangaStats->getCompleted());
        $this->assertGreaterThanOrEqual(23, $mangaStats->getOnHold());
        $this->assertGreaterThanOrEqual(0, $mangaStats->getDropped());
        $this->assertGreaterThanOrEqual(82, $mangaStats->getPlanToRead());
        $this->assertGreaterThanOrEqual(210, $mangaStats->getTotalEntries());
    }

    /**
     * @covers ::parseFriends
     */
    public function testParseFriends()
    {
        $friendsContent = file_get_contents(__DIR__.'/../InputSamples/profile-motokochan-friends.html');

        $friends = User::parseFriends($friendsContent);

        foreach ($friends as $friend) {
            if ($friend['name'] == 'AnimaSA') {
                break;
            }
        }

        $this->assertEquals('AnimaSA', $friend['name']);
        $this->assertEquals('2014-06-15T20:58-0700', $friend['friend_since']);
        $this->assertInstanceOf('Atarashii\ApiBundle\Model\Profile', $friend['profile']);
    }

    /**
     * @covers ::parseHistory
     */
    public function testParseHistory()
    {
        $historyContent = file_get_contents(__DIR__.'/../InputSamples/history-motokochan.html');

        $history = User::parseHistory($historyContent);

        $this->assertNotNull($history);
        $this->assertInternalType('array', $history);

        $historyItem = $history[0];

        $this->assertInstanceOf('Atarashii\ApiBundle\Model\Anime', $historyItem['item']);
        $this->assertEquals('anime', $historyItem['type']);
    }

    protected function setUp()
    {
        $profileContents = file_get_contents(__DIR__.'/../InputSamples/profile-motokochan.html');
        $profile2Contents = file_get_contents(__DIR__.'/../InputSamples/profile-xinil.html');

        $this->profile = User::parse($profileContents, '2.1');
        $this->profile2 = User::parse($profile2Contents, '2.1');
    }
}
