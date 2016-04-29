<?php

namespace Atarashii\APIBundle\Tests\Model;

use Atarashii\APIBundle\Model\AnimeStats;
use Atarashii\APIBundle\Model\MangaStats;
use Atarashii\APIBundle\Model\Profile;
use Atarashii\APIBundle\Model\ProfileDetails;

class ProfileTest extends \PHPUnit_Framework_TestCase
{
    public function testAvatarUrl()
    {
        $url = 'http://www.example.com/image.jpg';

        $profile = new Profile();
        $profile->setAvatarUrl($url);

        $this->assertEquals($url, $profile->getAvatarUrl());
    }

    public function testAccessRank()
    {
        $rank = rand();

        $profileDetails = new ProfileDetails();
        $profileDetails->setAccessRank($rank);

        $this->assertEquals($rank, $profileDetails->getAccessRank());
    }

    public function testLastOnline()
    {
        $now = new \DateTime('now');

        $profileDetails = new ProfileDetails();
        $profileDetails->setLastOnline('Now');

        $this->assertEquals('Now', $profileDetails->getLastOnline());
        $this->assertEquals($now->format(DATE_ISO8601), $profileDetails->getLastOnline2());
    }

    public function testStatus()
    {
        $status = 'Offline';

        $profileDetails = new ProfileDetails();
        $profileDetails->setStatus($status);

        $this->assertEquals($status, $profileDetails->getStatus());
    }

    public function testGender()
    {
        $gender = 'Not specified';

        $profileDetails = new ProfileDetails();
        $profileDetails->setGender($gender);

        $this->assertEquals($gender, $profileDetails->getGender());
    }

    public function testBirthday()
    {
        $birthday = 'December 12, 1982';

        $profileDetails = new ProfileDetails();
        $profileDetails->setBirthday($birthday);

        $this->assertEquals($birthday, $profileDetails->getBirthday());
    }

    public function testLocation()
    {
        $location = 'Space';

        $profileDetails = new ProfileDetails();
        $profileDetails->setLocation($location);

        $this->assertEquals($location, $profileDetails->getLocation());
    }

    public function testWebsite()
    {
        $website = 'http://atarashiiapp.com/';

        $profileDetails = new ProfileDetails();
        $profileDetails->setWebsite($website);

        $this->assertEquals($website, $profileDetails->getWebsite());
    }

    public function testJoinDate()
    {
        $joined = 'July 5, 2010';

        $profileDetails = new ProfileDetails();
        $profileDetails->setJoinDate($joined);

        $this->assertEquals($joined, $profileDetails->getJoinDate());
        $this->assertEquals('2010-07-05', $profileDetails->getJoinDate2());
    }

    public function testAnimeListViews()
    {
        $views = rand();

        $profileDetails = new ProfileDetails();
        $profileDetails->setAnimeListViews($views);

        $this->assertEquals($views, $profileDetails->getAnimeListViews());
    }

    public function testMangaListViews()
    {
        $views = rand();

        $profileDetails = new ProfileDetails();
        $profileDetails->setMangaListViews($views);

        $this->assertEquals($views, $profileDetails->getMangaListViews());
    }

    public function testForumPosts()
    {
        $posts = rand();

        $profileDetails = new ProfileDetails();
        $profileDetails->setForumPosts($posts);

        $this->assertEquals($posts, $profileDetails->getForumPosts());
    }

    public function testReviews()
    {
        $posts = rand();

        $profileDetails = new ProfileDetails();
        $profileDetails->setReviews($posts);

        $this->assertEquals($posts, $profileDetails->getReviews());
    }

    public function testRecommedations()
    {
        $posts = rand();

        $profileDetails = new ProfileDetails();
        $profileDetails->setRecommedations($posts);

        $this->assertEquals($posts, $profileDetails->getRecommedations());
    }

    public function testBlogPosts()
    {
        $posts = rand();

        $profileDetails = new ProfileDetails();
        $profileDetails->setBlogPosts($posts);

        $this->assertEquals($posts, $profileDetails->getBlogPosts());
    }

    public function testClubs()
    {
        $posts = rand();

        $profileDetails = new ProfileDetails();
        $profileDetails->setClubs($posts);

        $this->assertEquals($posts, $profileDetails->getClubs());
    }

    public function testComments()
    {
        $comments = rand();

        $profileDetails = new ProfileDetails();
        $profileDetails->setComments($comments);

        $this->assertEquals($comments, $profileDetails->getComments());
    }

    public function testAim()
    {
        $value = 'Name';

        $profileDetails = new ProfileDetails();
        $profileDetails->setAim($value);

        $this->assertEquals($value, $profileDetails->getAim());
    }

    public function testMsn()
    {
        $value = 'Name';

        $profileDetails = new ProfileDetails();
        $profileDetails->setMsn($value);

        $this->assertEquals($value, $profileDetails->getMsn());
    }

    public function testYahoo()
    {
        $value = 'Name';

        $profileDetails = new ProfileDetails();
        $profileDetails->setYahoo($value);

        $this->assertEquals($value, $profileDetails->getYahoo());
    }

    public function testAnimeTimeDays()
    {
        $days = number_format(rand(1, 800) + (rand() / getrandmax()), 1);

        $animeStats = new AnimeStats();
        $animeStats->setTimeDays($days);

        $this->assertEquals($days, $animeStats->getTimeDays());
    }

    public function testAnimeMeanScore()
    {
        $days = number_format(rand(1, 800) + (rand() / getrandmax()), 1);

        $animeStats = new AnimeStats();
        $animeStats->setMeanScore($days);

        $this->assertEquals($days, $animeStats->getMeanScore());
    }

    public function testAnimeWatching()
    {
        $value = rand();

        $animeStats = new AnimeStats();
        $animeStats->setWatching($value);

        $this->assertEquals($value, $animeStats->getWatching());
    }

    public function testAnimeCompleted()
    {
        $value = rand();

        $animeStats = new AnimeStats();
        $animeStats->setCompleted($value);

        $this->assertEquals($value, $animeStats->getCompleted());
    }

    public function testAnimeOnHold()
    {
        $value = rand();

        $animeStats = new AnimeStats();
        $animeStats->setOnHold($value);

        $this->assertEquals($value, $animeStats->getOnHold());
    }

    public function testAnimeDropped()
    {
        $value = rand();

        $animeStats = new AnimeStats();
        $animeStats->setDropped($value);

        $this->assertEquals($value, $animeStats->getDropped());
    }

    public function testAnimePlanToWatch()
    {
        $value = rand();

        $animeStats = new AnimeStats();
        $animeStats->setPlanToWatch($value);

        $this->assertEquals($value, $animeStats->getPlanToWatch());
    }

    public function testAnimeTotalEntriesAnime()
    {
        $entries = rand();

        $animeStats = new AnimeStats();
        $animeStats->setTotalEntries($entries);

        $this->assertEquals($entries, $animeStats->getTotalEntries());
    }

    public function testAnimeRewatched()
    {
        $entries = rand();

        $AnimeStats = new AnimeStats();
        $AnimeStats->setRewatched($entries);

        $this->assertEquals($entries, $AnimeStats->getRewatched());
    }

    public function testAnimeEpisodes()
    {
        $entries = rand();

        $AnimeStats = new AnimeStats();
        $AnimeStats->setEpisodes($entries);

        $this->assertEquals($entries, $AnimeStats->getEpisodes());
    }

    public function testMangaTimeDays()
    {
        $days = number_format(rand(1, 800) + (rand() / getrandmax()), 1);

        $mangaStats = new MangaStats();
        $mangaStats->setTimeDays($days);

        $this->assertEquals($days, $mangaStats->getTimeDays());
    }

    public function testMangaMeanScore()
    {
        $days = number_format(rand(1, 800) + (rand() / getrandmax()), 1);

        $mangaStats = new MangaStats();
        $mangaStats->setMeanScore($days);

        $this->assertEquals($days, $mangaStats->getMeanScore());
    }

    public function testMangaReading()
    {
        $value = rand();

        $mangaStats = new MangaStats();
        $mangaStats->setReading($value);

        $this->assertEquals($value, $mangaStats->getReading());
    }

    public function testMangaCompleted()
    {
        $value = rand();

        $mangaStats = new MangaStats();
        $mangaStats->setCompleted($value);

        $this->assertEquals($value, $mangaStats->getCompleted());
    }

    public function testMangaOnHold()
    {
        $value = rand();

        $mangaStats = new MangaStats();
        $mangaStats->setOnHold($value);

        $this->assertEquals($value, $mangaStats->getOnHold());
    }

    public function testMangaDropped()
    {
        $value = rand();

        $mangaStats = new MangaStats();
        $mangaStats->setDropped($value);

        $this->assertEquals($value, $mangaStats->getDropped());
    }

    public function testMangaPlanToRead()
    {
        $value = rand();

        $mangaStats = new MangaStats();
        $mangaStats->setPlanToRead($value);

        $this->assertEquals($value, $mangaStats->getPlanToRead());
    }

    public function testMangaTotalEntriesManga()
    {
        $entries = rand();

        $mangaStats = new MangaStats();
        $mangaStats->setTotalEntries($entries);

        $this->assertEquals($entries, $mangaStats->getTotalEntries());
    }

    public function testMangaReread()
    {
        $entries = rand();

        $mangaStats = new MangaStats();
        $mangaStats->setReread($entries);

        $this->assertEquals($entries, $mangaStats->getReread());
    }

    public function testMangaVolumes()
    {
        $entries = rand();

        $mangaStats = new MangaStats();
        $mangaStats->setVolumes($entries);

        $this->assertEquals($entries, $mangaStats->getVolumes());
    }
}
