<?php

namespace Atarashii\APIBundle\Tests\Model;

use Atarashii\APIBundle\Model\Anime;

class AnimeTest extends \PHPUnit_Framework_TestCase
{
    public function testId()
    {
        $animeId = rand();

        $anime = new Anime();
        $anime->setId($animeId);

        $this->assertEquals($animeId, $anime->getId());
    }

    public function testTitle()
    {
        $animeTitle = 'The Title';

        $anime = new Anime();
        $anime->setTitle($animeTitle);

        $this->assertEquals($animeTitle, $anime->getTitle());
    }

    public function testPreview()
    {
        $animePreview = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';

        $anime = new Anime();
        $anime->setPreview($animePreview);

        $this->assertEquals($animePreview, $anime->getPreview());
    }

    public function testOtherTitles()
    {
        $anime = new Anime();

        $otherTitles = array();
        $otherTitles['english'][] = '5 Centimeters per Second';
        $otherTitles['synonyms'][] = 'Five Centimeters Per Second';
        $otherTitles['synonyms'][] = 'Byousoku 5 Centimeter - a chain of short stories about their distance';
        $otherTitles['synonyms'][] = '5 Centimetres Per Second';
        $otherTitles['synonyms'][] = '5 cm per second';
        $otherTitles['japanese'][] = '秒速５センチメートル';

        $anime->setOtherTitles($otherTitles);

        $this->assertEquals($otherTitles, $anime->getOtherTitles());
    }

    public function testRank()
    {
        $animeRank = rand();

        $anime = new Anime();
        $anime->setRank($animeRank);

        $this->assertEquals($animeRank, $anime->getRank());
    }

    public function testPopularityRank()
    {
        $animeRank = rand();

        $anime = new Anime();
        $anime->setPopularityRank($animeRank);

        $this->assertEquals($animeRank, $anime->getPopularityRank());
    }

    public function testImageUrl()
    {
        $animeImgUrl = 'http://www.example.com/image.jpg';

        $anime = new Anime();
        $anime->setImageUrl($animeImgUrl);

        $this->assertEquals($animeImgUrl, $anime->getImageUrl());
    }

    public function testType()
    {
        $anime = new Anime();

        /*
         * Integers
         */
        $animeType = 1;
        $anime->setType($animeType);
        $this->assertEquals('TV', $anime->getType());

        $animeType = 2;
        $anime->setType($animeType);
        $this->assertEquals('OVA', $anime->getType());

        $animeType = 3;
        $anime->setType($animeType);
        $this->assertEquals('Movie', $anime->getType());

        $animeType = 4;
        $anime->setType($animeType);
        $this->assertEquals('Special', $anime->getType());

        $animeType = 5;
        $anime->setType($animeType);
        $this->assertEquals('ONA', $anime->getType());

        $animeType = 6;
        $anime->setType($animeType);
        $this->assertEquals('Music', $anime->getType());

        /*
         * Strings
         */
        $animeType = 'TV';
        $anime->setType($animeType);
        $this->assertEquals($animeType, $anime->getType());

        $animeType = 'OVA';
        $anime->setType($animeType);
        $this->assertEquals($animeType, $anime->getType());

        $animeType = 'Movie';
        $anime->setType($animeType);
        $this->assertEquals($animeType, $anime->getType());

        $animeType = 'Special';
        $anime->setType($animeType);
        $this->assertEquals($animeType, $anime->getType());

        $animeType = 'ONA';
        $anime->setType($animeType);
        $this->assertEquals($animeType, $anime->getType());

        $animeType = 'Music';
        $anime->setType($animeType);
        $this->assertEquals($animeType, $anime->getType());

        /*
         * Default Value
         */
        $animeType = 'Invalid';
        $anime->setType($animeType);
        $this->assertEquals('TV', $anime->getType());
    }

    public function testEpisodes()
    {
        $episodes = rand();

        $anime = new Anime();
        $anime->setEpisodes($episodes);

        $this->assertEquals($episodes, $anime->getEpisodes());
    }

    public function testStatus()
    {
        $anime = new Anime();

        /*
         * Integers
         */
        $animeStatus = 1;
        $anime->setStatus($animeStatus);
        $this->assertEquals('currently airing', $anime->getStatus());

        $animeStatus = 2;
        $anime->setStatus($animeStatus);
        $this->assertEquals('finished airing', $anime->getStatus());

        $animeStatus = 3;
        $anime->setStatus($animeStatus);
        $this->assertEquals('not yet aired', $anime->getStatus());

        /*
         * Strings
         */
        $animeStatus = 'currently airing';
        $anime->setStatus($animeStatus);
        $this->assertEquals($animeStatus, $anime->getStatus());

        $animeStatus = 'finished airing';
        $anime->setStatus($animeStatus);
        $this->assertEquals($animeStatus, $anime->getStatus());

        $animeStatus = 'not yet aired';
        $anime->setStatus($animeStatus);
        $this->assertEquals($animeStatus, $anime->getStatus());

        /*
         * Default Value
         */
        $animeStatus = 'Invalid';
        $anime->setStatus($animeStatus);
        $this->assertEquals('finished airing', $anime->getStatus());
    }

    public function testStartDate()
    {
        $startDate = new \DateTime('now');

        /*
         * Literals
         */
        // Day
        $anime = new Anime();
        $anime->setLiteralStartDate('', $startDate, 'day');
        $verifyDate = $startDate->format('Y-m-d');
        $this->assertEquals($verifyDate, $anime->getStartDate());

        // Month
        $anime = new Anime();
        $anime->setLiteralStartDate('', $startDate, 'month');
        $verifyDate = $startDate->format('Y-m');
        $this->assertEquals($verifyDate, $anime->getStartDate());

        // Day
        $anime = new Anime();
        $anime->setLiteralStartDate('', $startDate, 'year');
        $verifyDate = $startDate->format('Y');
        $this->assertEquals($verifyDate, $anime->getStartDate());

        /*
         * Normal Set
         */
        // Day
        $anime = new Anime();
        $anime->setStartDate($startDate, 'day');
        $verifyDate = $startDate->format('Y-m-d');
        $this->assertEquals($verifyDate, $anime->getStartDate());

        // Month
        $anime = new Anime();
        $anime->setStartDate($startDate, 'month');
        $verifyDate = $startDate->format('Y-m');
        $this->assertEquals($verifyDate, $anime->getStartDate());

        // Day
        $anime = new Anime();
        $anime->setStartDate($startDate, 'year');
        $verifyDate = $startDate->format('Y');
        $this->assertEquals($verifyDate, $anime->getStartDate());
    }

    public function testEndDate()
    {
        $EndDate = new \DateTime('now');

        /*
         * Literals
         */
        // Day
        $anime = new Anime();
        $anime->setLiteralEndDate('', $EndDate, 'day');
        $verifyDate = $EndDate->format('Y-m-d');
        $this->assertEquals($verifyDate, $anime->getEndDate());

        // Month
        $anime = new Anime();
        $anime->setLiteralEndDate('', $EndDate, 'month');
        $verifyDate = $EndDate->format('Y-m');
        $this->assertEquals($verifyDate, $anime->getEndDate());

        // Day
        $anime = new Anime();
        $anime->setLiteralEndDate('', $EndDate, 'year');
        $verifyDate = $EndDate->format('Y');
        $this->assertEquals($verifyDate, $anime->getEndDate());

        /*
         * Normal Set
         */
        // Day
        $anime = new Anime();
        $anime->setEndDate($EndDate, 'day');
        $verifyDate = $EndDate->format('Y-m-d');
        $this->assertEquals($verifyDate, $anime->getEndDate());

        // Month
        $anime = new Anime();
        $anime->setEndDate($EndDate, 'month');
        $verifyDate = $EndDate->format('Y-m');
        $this->assertEquals($verifyDate, $anime->getEndDate());

        // Day
        $anime = new Anime();
        $anime->setEndDate($EndDate, 'year');
        $verifyDate = $EndDate->format('Y');
        $this->assertEquals($verifyDate, $anime->getEndDate());
    }

    public function testBroadcast()
    {
        $date = new \DateTime('now');

        $anime = new Anime();
        $anime->setBroadcast($date->format('l H:i T'));

        $this->assertEquals($date->format('Y-m-d\TH:iO'), $anime->getBroadcast());
    }

    public function testDuration()
    {
        $animeDuration = 75;

        $anime = new Anime();
        $anime->setDuration($animeDuration);

        $this->assertEquals($animeDuration, $anime->getDuration());
    }

    public function testWatchedStatus()
    {
        $anime = new Anime();

        /*
         * Integers
         */
        $animeWatchedStatus = 1;
        $anime->setWatchedStatus($animeWatchedStatus);
        $this->assertEquals($animeWatchedStatus, $anime->getWatchedStatus('int'));
        $this->assertEquals('watching', $anime->getWatchedStatus());

        $animeWatchedStatus = 2;
        $anime->setWatchedStatus($animeWatchedStatus);
        $this->assertEquals($animeWatchedStatus, $anime->getWatchedStatus('int'));
        $this->assertEquals('completed', $anime->getWatchedStatus());

        $animeWatchedStatus = 3;
        $anime->setWatchedStatus($animeWatchedStatus);
        $this->assertEquals($animeWatchedStatus, $anime->getWatchedStatus('int'));
        $this->assertEquals('on-hold', $anime->getWatchedStatus());

        $animeWatchedStatus = 4;
        $anime->setWatchedStatus($animeWatchedStatus);
        $this->assertEquals($animeWatchedStatus, $anime->getWatchedStatus('int'));
        $this->assertEquals('dropped', $anime->getWatchedStatus());

        $animeWatchedStatus = 6;
        $anime->setWatchedStatus($animeWatchedStatus);
        $this->assertEquals($animeWatchedStatus, $anime->getWatchedStatus('int'));
        $this->assertEquals('plan to watch', $anime->getWatchedStatus());

        /*
         * Strings
         */
        $animeWatchedStatus = 'watching';
        $anime->setWatchedStatus($animeWatchedStatus);
        $this->assertEquals($animeWatchedStatus, $anime->getWatchedStatus());

        $animeWatchedStatus = 'completed';
        $anime->setWatchedStatus($animeWatchedStatus);
        $this->assertEquals($animeWatchedStatus, $anime->getWatchedStatus());

        $animeWatchedStatus = 'on-hold';
        $anime->setWatchedStatus($animeWatchedStatus);
        $this->assertEquals($animeWatchedStatus, $anime->getWatchedStatus());

        $animeWatchedStatus = 'onhold';
        $anime->setWatchedStatus($animeWatchedStatus);
        $this->assertEquals('on-hold', $anime->getWatchedStatus());

        $animeWatchedStatus = 'dropped';
        $anime->setWatchedStatus($animeWatchedStatus);
        $this->assertEquals($animeWatchedStatus, $anime->getWatchedStatus());

        $animeWatchedStatus = 'plan to watch';
        $anime->setWatchedStatus($animeWatchedStatus);
        $this->assertEquals($animeWatchedStatus, $anime->getWatchedStatus());

        $animeWatchedStatus = 'plantowatch';
        $anime->setWatchedStatus($animeWatchedStatus);
        $this->assertEquals('plan to watch', $anime->getWatchedStatus());

        /*
         * Default Value
         */
        $animeWatchedStatus = 'Invalid';
        $anime->setWatchedStatus($animeWatchedStatus);
        $this->assertEquals('watching', $anime->getWatchedStatus());
    }

    public function testPriority()
    {
        $anime = new Anime();

        /*
         * Integers
         */
        $animePriority = 0;
        $anime->setPriority($animePriority);
        $this->assertEquals('Low', $anime->getPriority('string'));
        $this->assertEquals($animePriority, $anime->getPriority());

        $animePriority = 1;
        $anime->setPriority($animePriority);
        $this->assertEquals('Medium', $anime->getPriority('string'));
        $this->assertEquals($animePriority, $anime->getPriority());

        $animePriority = 2;
        $anime->setPriority($animePriority);
        $this->assertEquals('High', $anime->getPriority('string'));
        $this->assertEquals($animePriority, $anime->getPriority());

        /*
         * Default Value
         */
        $animePriority = '27';
        $anime->setPriority($animePriority);
        $this->assertEquals('Low', $anime->getPriority('string'));
        $this->assertEquals('0', $anime->getPriority());
    }

    public function testClassification()
    {
        $classification = 'PG-13 - Teens 13 or older';

        $anime = new Anime();
        $anime->setClassification($classification);

        $this->assertEquals($classification, $anime->getClassification());
    }

    public function testMembersScore()
    {
        $score = 7.95;

        $anime = new Anime();
        $anime->setMembersScore($score);

        $this->assertEquals($score, $anime->getMembersScore());

        //Try inserting bad data
        $score = 'Fake';

        $anime->setMembersScore($score);

        $this->assertEquals((float) $score, $anime->getMembersScore());
    }

    public function testMembersCount()
    {
        $count = 238793;

        $anime = new Anime();
        $anime->setMembersCount($count);

        $this->assertEquals($count, $anime->getMembersCount());

        //Try inserting bad data
        $count = 'Fake';

        $anime->setMembersCount($count);

        $this->assertEquals((int) $count, $anime->getMembersCount());
    }

    public function testFavoritedCount()
    {
        $favs = 7295;

        $anime = new Anime();
        $anime->setFavoritedCount($favs);

        $this->assertEquals($favs, $anime->getFavoritedCount());

        //Try inserting bad data
//        $favs = 'Fake';
//
//        $anime->setFavoritedCount($favs);
//
//        $this->assertEquals((int) $favs, $anime->getFavoritedCount());
    }

    public function testExternalLinks()
    {
        $animerecord = new Anime();
        $animerecord->setExternalLinks('Atarashii-API', 'https://bitbucket.org/ratan12/atarashii-api');

        $externalLinks = array();
        $externalLinks['Atarashii-API'] = 'https://bitbucket.org/ratan12/atarashii-api';

        $this->assertEquals($externalLinks, $animerecord->getExternalLinks());
    }

    public function testSynopsis()
    {
        $synopsis = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce fringilla, nisi sed rutrum interdum, neque neque tincidunt eros, ac euismod enim massa a quam. Vivamus maximus enim ac odio euismod tristique. Suspendisse potenti. Praesent a nulla tortor. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aenean varius metus at sapien tempor auctor. Donec semper odio sed posuere placerat.';

        $anime = new Anime();
        $anime->setSynopsis($synopsis);

        $this->assertEquals($synopsis, $anime->getSynopsis());

        //Test unicode
        $synopsis = '漫研で夏コミへのサークル参加を決めたが……時間がない！　恋ヶ崎にムラサキさん、小豆ちゃんも手伝ってくれることになり、合宿を行うも——あれ、お酒で変なテンションになってない!?　……締切、間に合うよね？';

        $anime->setSynopsis($synopsis);

        $this->assertEquals($synopsis, $anime->getSynopsis());
    }

    public function testBackground()
    {
        $synopsis = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce fringilla, nisi sed rutrum interdum, neque neque tincidunt eros, ac euismod enim massa a quam. Vivamus maximus enim ac odio euismod tristique. Suspendisse potenti. Praesent a nulla tortor. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aenean varius metus at sapien tempor auctor. Donec semper odio sed posuere placerat.';

        $anime = new Anime();
        $anime->setBackground($synopsis);

        $this->assertEquals($synopsis, $anime->getBackground());

        //Test unicode
        $synopsis = '漫研で夏コミへのサークル参加を決めたが……時間がない！　恋ヶ崎にムラサキさん、小豆ちゃんも手伝ってくれることになり、合宿を行うも——あれ、お酒で変なテンションになってない!?　……締切、間に合うよね？';

        $anime->setBackground($synopsis);

        $this->assertEquals($synopsis, $anime->getBackground());
    }

    public function testProducers()
    {
        $producers = 'Kyoto Animation, FUNimation Entertainment, Lantis, Rakuonsha, Kadokawa Pictures USA, Lucky Paradise';

        $anime = new Anime();
        $anime->setProducers($producers);

        $this->assertEquals($producers, $anime->getProducers());
    }

    public function testGenres()
    {
        $genres = 'Comedy, Parody, School, Slice of Life';

        $anime = new Anime();
        $anime->setGenres($genres);

        $this->assertEquals($genres, $anime->getGenres());
    }

    public function testMangaAdaptations()
    {
        $relation = array();
        $relation['manga_id'] = rand();
        $relation['title'] = 'This is a title';
        $relation['url'] = 'http://myanimelist.net/manga/'.$relation['manga_id'];

        $anime = new Anime();
        $anime->addRelation($relation, 'adaptation');

        $adaptations = $anime->getMangaAdaptations();

        $this->assertEquals($relation, $adaptations[0]);
    }

    public function testPrequels()
    {
        $relation = array();
        $relation['anime_id'] = rand();
        $relation['title'] = 'This is a title';
        $relation['url'] = 'http://myanimelist.net/anime/'.$relation['anime_id'];

        $anime = new Anime();
        $anime->addRelation($relation, 'prequel');

        $prequels = $anime->getPrequels();

        $this->assertEquals($relation, $prequels[0]);
    }

    public function testSequels()
    {
        $relation = array();
        $relation['anime_id'] = rand();
        $relation['title'] = 'This is a title';
        $relation['url'] = 'http://myanimelist.net/anime/'.$relation['anime_id'];

        $anime = new Anime();
        $anime->addRelation($relation, 'sequel');

        $sequels = $anime->getSequels();

        $this->assertEquals($relation, $sequels[0]);
    }

    public function testSideStories()
    {
        $relation = array();
        $relation['anime_id'] = rand();
        $relation['title'] = 'This is a title';
        $relation['url'] = 'http://myanimelist.net/anime/'.$relation['anime_id'];

        $anime = new Anime();
        $anime->addRelation($relation, 'side_story');

        $sideStories = $anime->getSideStories();

        $this->assertEquals($relation, $sideStories[0]);
    }

    public function testParentStory()
    {
        $relation = array();
        $relation['anime_id'] = rand();
        $relation['title'] = 'This is a title';
        $relation['url'] = 'http://myanimelist.net/anime/'.$relation['anime_id'];

        $anime = new Anime();
        $anime->addRelation($relation, 'parent_story');

        $parentStory = $anime->getParentStory();

        $this->assertEquals($relation, $parentStory);
    }

    public function testCharacterAnime()
    {
        $relation = array();
        $relation['anime_id'] = rand();
        $relation['title'] = 'This is a title';
        $relation['url'] = 'http://myanimelist.net/anime/'.$relation['anime_id'];

        $anime = new Anime();
        $anime->addRelation($relation, 'character');

        $characterAnime = $anime->getCharacterAnime();

        $this->assertEquals($relation, $characterAnime[0]);
    }

    public function testSpinoffs()
    {
        $relation = array();
        $relation['anime_id'] = rand();
        $relation['title'] = 'This is a title';
        $relation['url'] = 'http://myanimelist.net/anime/'.$relation['anime_id'];

        $anime = new Anime();
        $anime->addRelation($relation, 'spin-off');

        $spinOffs = $anime->getSpinOffs();

        $this->assertEquals($relation, $spinOffs[0]);
    }

    public function testSummaries()
    {
        $relation = array();
        $relation['anime_id'] = rand();
        $relation['title'] = 'This is a title';
        $relation['url'] = 'http://myanimelist.net/anime/'.$relation['anime_id'];

        $anime = new Anime();
        $anime->addRelation($relation, 'summary');

        $summaries = $anime->getSummaries();

        $this->assertEquals($relation, $summaries[0]);
    }

    public function testAlternativeVersions()
    {
        $relation = array();
        $relation['anime_id'] = rand();
        $relation['title'] = 'This is a title';
        $relation['url'] = 'http://myanimelist.net/anime/'.$relation['anime_id'];

        $anime = new Anime();
        $anime->addRelation($relation, 'alternative_version');

        $alternativeVersions = $anime->getAlternativeVersions();

        $this->assertEquals($relation, $alternativeVersions[0]);
    }

    public function testOther()
    {
        $relation = array();
        $relation['anime_id'] = rand();
        $relation['title'] = 'This is a title';
        $relation['url'] = 'http://myanimelist.net/anime/'.$relation['anime_id'];

        $anime = new Anime();
        $anime->addRelation($relation, 'other');

        $other = $anime->getOther();

        $this->assertEquals($relation, $other[0]);
    }

    public function testOpeningTheme()
    {
        $OP = array();
        $OP[] = '#10: "We Are (ウィーアー! 〜10周年Ver.〜)" by TVXQ (eps 373-394)';
        $OP[] = '#04: "Sailor Fuku to Kikanjuu (セーラー服と機関銃)" by Emiri Kato (ep 4)';

        $anime = new Anime();
        $anime->setOpeningTheme('#10: "We Are (ウィーアー! 〜10周年Ver.〜)" by TVXQ (eps 373-394)');
        $anime->setOpeningTheme('#04: "Sailor Fuku to Kikanjuu (セーラー服と機関銃)" by Emiri Kato (ep 4)');

        $this->assertEquals($OP, $anime->getOpeningTheme());
    }

    public function testEndingTheme()
    {
        $OP = array();
        $OP[] = '#10: "We Are (ウィーアー! 〜10周年Ver.〜)" by TVXQ (eps 373-394)';
        $OP[] = '#04: "Sailor Fuku to Kikanjuu (セーラー服と機関銃)" by Emiri Kato (ep 4)';

        $anime = new Anime();
        $anime->setEndingTheme('#10: "We Are (ウィーアー! 〜10周年Ver.〜)" by TVXQ (eps 373-394)');
        $anime->setEndingTheme('#04: "Sailor Fuku to Kikanjuu (セーラー服と機関銃)" by Emiri Kato (ep 4)');

        $this->assertEquals($OP, $anime->getEndingTheme());
    }

    public function testRecommendations()
    {
        $animerecord = new Anime();

        $anime = new Anime();
        $anime->setId(rand());
        $anime->setTitle('This is a title');
        $anime->setImageUrl('https://myanimelist.cdn-dena.com/images/anime/5/18179.jpg');
        $animerecord->setRecommendations($anime);

        $recommendations = $animerecord->getRecommendations();
        $this->assertEquals($anime, $recommendations[0]);
    }

    public function testWatchedEpisode()
    {
        $episode = rand();

        $anime = new Anime();
        $anime->setWatchedEpisodes($episode);

        $this->assertEquals($episode, $anime->getWatchedEpisodes());
    }

    public function testScore()
    {
        $score = rand(0, 10);

        $anime = new Anime();
        $anime->setScore($score);

        $this->assertEquals($score, $anime->getScore());
    }

    public function testListedAnimeId()
    {
        $animeId = rand();

        $anime = new Anime();
        $anime->setListedAnimeId($animeId);

        $this->assertEquals($animeId, $anime->getListedAnimeId());
    }

    public function testPersonalTags()
    {
        $tags = 'one, two, three, four';

        $anime = new Anime();
        $anime->setPersonalTags($tags);

        $this->assertEquals($tags, $anime->getPersonalTags());
    }

    public function testWatchingStart()
    {
        $now = new \DateTime('now');
        $watchDate = $now->format('Y-m-d');

        $anime = new Anime();
        $anime->setWatchingStart($watchDate);

        $this->assertEquals($watchDate, $anime->getWatchingStart());
    }

    public function testWatchingEnd()
    {
        $now = new \DateTime('now');
        $watchDate = $now->format('Y-m-d');

        $anime = new Anime();
        $anime->setWatchingEnd($watchDate);

        $this->assertEquals($watchDate, $anime->getWatchingEnd());
    }

    public function testStorage()
    {
        $anime = new Anime();

        /*
         * Integers
         */
        $animeStorage = 1;
        $anime->setStorage($animeStorage);
        $this->assertEquals($animeStorage, $anime->getStorage());
        $this->assertEquals('Hard Drive', $anime->getStorage('string'));

        $animeStorage = 2;
        $anime->setStorage($animeStorage);
        $this->assertEquals($animeStorage, $anime->getStorage());
        $this->assertEquals('DVD / CD', $anime->getStorage('string'));

        $animeStorage = 3;
        $anime->setStorage($animeStorage);
        $this->assertEquals($animeStorage, $anime->getStorage());
        $this->assertEquals('None', $anime->getStorage('string'));

        $animeStorage = 4;
        $anime->setStorage($animeStorage);
        $this->assertEquals($animeStorage, $anime->getStorage());
        $this->assertEquals('Retail DVD', $anime->getStorage('string'));

        $animeStorage = 5;
        $anime->setStorage($animeStorage);
        $this->assertEquals($animeStorage, $anime->getStorage());
        $this->assertEquals('VHS', $anime->getStorage('string'));

        $animeStorage = 6;
        $anime->setStorage($animeStorage);
        $this->assertEquals($animeStorage, $anime->getStorage());
        $this->assertEquals('External HD', $anime->getStorage('string'));

        $animeStorage = 7;
        $anime->setStorage($animeStorage);
        $this->assertEquals($animeStorage, $anime->getStorage());
        $this->assertEquals('NAS', $anime->getStorage('string'));

        // Bad data
        $animeStorage = 0;
        $anime->setStorage($animeStorage);
        $this->assertEquals(null, $anime->getStorage());
        $this->assertEquals(null, $anime->getStorage('string'));

        $animeStorage = 'Fake';
        $anime->setStorage($animeStorage);
        $this->assertEquals(null, $anime->getStorage());
        $this->assertEquals(null, $anime->getStorage('string'));
    }

    public function testStorageValue()
    {
        $anime = new Anime();

        //Get a random float between 1 and 800 with two decimal places
        $storageValue = number_format(rand(1, 800) + (rand() / getrandmax()), 2);

        $anime->setStorageValue($storageValue);

        $this->assertEquals($storageValue, $anime->getStorageValue());

        //Invalid Data
        $storageValue = 'Fake';

        $anime->setStorageValue($storageValue);

        $this->assertEquals((float) $storageValue, $anime->getStorageValue());
    }

    public function testEpsDownloaded()
    {
        $eps = rand();

        $anime = new Anime();
        $anime->setEpsDownloaded($eps);

        $this->assertEquals($eps, $anime->getEpsDownloaded());
    }

    public function testRewatching()
    {
        $anime = new Anime();

        //Using Real boolean
        $isRewatch = true;
        $anime->setRewatching($isRewatch);
        $this->assertEquals($isRewatch, $anime->getRewatching());

        //Using integers
        $isRewatch = 1;
        $anime->setRewatching($isRewatch);
        $this->assertEquals(true, $anime->getRewatching());

        //Invalid Data
        $isRewatch = 'Birdy';
        $anime->setRewatching($isRewatch);
        $this->assertEquals((bool) $isRewatch, $anime->getRewatching());
    }

    public function testRewatchCount()
    {
        $count = rand();

        $anime = new Anime();
        $anime->setRewatchCount($count);

        $this->assertEquals($count, $anime->getRewatchCount());
    }

    public function testRewatchValue()
    {
        $anime = new Anime();

        /*
         * Integers
         */
        $animeRewatchValue = 1;
        $anime->setRewatchValue($animeRewatchValue);
        $this->assertEquals($animeRewatchValue, $anime->getRewatchValue());
        $this->assertEquals('Very Low', $anime->getRewatchValue('string'));

        $animeRewatchValue = 2;
        $anime->setRewatchValue($animeRewatchValue);
        $this->assertEquals($animeRewatchValue, $anime->getRewatchValue());
        $this->assertEquals('Low', $anime->getRewatchValue('string'));

        $animeRewatchValue = 3;
        $anime->setRewatchValue($animeRewatchValue);
        $this->assertEquals($animeRewatchValue, $anime->getRewatchValue());
        $this->assertEquals('Medium', $anime->getRewatchValue('string'));

        $animeRewatchValue = 4;
        $anime->setRewatchValue($animeRewatchValue);
        $this->assertEquals($animeRewatchValue, $anime->getRewatchValue());
        $this->assertEquals('High', $anime->getRewatchValue('string'));

        $animeRewatchValue = 5;
        $anime->setRewatchValue($animeRewatchValue);
        $this->assertEquals($animeRewatchValue, $anime->getRewatchValue());
        $this->assertEquals('Very High', $anime->getRewatchValue('string'));

        // Bad data
        $animeRewatchValue = 0;
        $anime->setRewatchValue($animeRewatchValue);
        $this->assertEquals(null, $anime->getRewatchValue());
        $this->assertEquals(null, $anime->getRewatchValue('string'));

        $animeRewatchValue = 'Fake';
        $anime->setRewatchValue($animeRewatchValue);
        $this->assertEquals(null, $anime->getRewatchValue());
        $this->assertEquals(null, $anime->getRewatchValue('string'));
    }

    public function testPersonalComments()
    {
        $comments = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce fringilla, nisi sed rutrum interdum, neque neque tincidunt eros, ac euismod enim massa a quam. Vivamus maximus enim ac odio euismod tristique. Suspendisse potenti. Praesent a nulla tortor. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aenean varius metus at sapien tempor auctor. Donec semper odio sed posuere placerat.';

        $anime = new Anime();
        $anime->setPersonalComments($comments);

        $this->assertEquals($comments, $anime->getPersonalComments());

        //Test unicode
        $comments = '漫研で夏コミへのサークル参加を決めたが……時間がない！　恋ヶ崎にムラサキさん、小豆ちゃんも手伝ってくれることになり、合宿を行うも——あれ、お酒で変なテンションになってない!?　……締切、間に合うよね？';

        $anime->setPersonalComments($comments);

        $this->assertEquals($comments, $anime->getPersonalComments());
    }

    //These functions being tested shouldn't be used anymore, MAL has phased the data out
    //Keeping tests in until they are removed from the model.

    public function testTags()
    {
        $anime = new Anime();

        $tags[] = 'one';
        $tags[] = 'two';
        $anime->setTags('one');
        $anime->setTags('two');

        $this->assertEquals($tags, $anime->getTags());
    }

    public function testFansubGroup()
    {
        $anime = new Anime();

        $group = 'Static-Subs';
        $anime->setFansubGroup($group);

        $this->assertEquals($group, $anime->getFansubGroup());
    }

    //It might be better to try and do an XML compare in the future.
    public function testMalXml()
    {
        $anime = new Anime();
        $items = array();

        $output = "<?xml version=\"1.0\"?>\n<entry><episode>7</episode><status>1</status><score>9</score><downloaded_episodes>8</downloaded_episodes><storage_type>2</storage_type><storage_value>3.7</storage_value><times_rewatched>1</times_rewatched><rewatch_value>4</rewatch_value><date_start>01012015</date_start><date_finish>01022015</date_finish><priority>0</priority><comments>This is a comment.</comments><fansub_group>GG</fansub_group><tags>one,two,three</tags></entry>\n";

        $anime->setId(1);

        $anime->setWatchedEpisodes(7);
        $items[] = 'episodes';

        $anime->setWatchedStatus(1);
        $items[] = 'status';

        $anime->setScore(9);
        $items[] = 'score';

        $anime->setEpsDownloaded(8);
        $items[] = 'downloaded';

        $anime->setStorage(2);
        $items[] = 'storage';

        $anime->setStorageValue(3.7);
        $items[] = 'storageAmt';

        $anime->setRewatchCount(1);
        $items[] = 'rewatchCount';

        $anime->setRewatchValue(4);
        $items[] = 'rewatchValue';

        $anime->setWatchingStart(new \DateTime('20150101'));
        $items[] = 'start';

        $anime->setWatchingEnd(new \DateTime('20150102'));
        $items[] = 'end';

        $anime->setPriority(6);
        $items[] = 'priority';

        $anime->setPersonalComments('This is a comment.');
        $items[] = 'comments';

        $anime->setFansubGroup('GG');
        $items[] = 'fansubber';

        $anime->setPersonalTags('one,two,three');
        $items[] = 'tags';

        $this->assertEquals($output, $anime->MALApiXml($items));
    }
}
