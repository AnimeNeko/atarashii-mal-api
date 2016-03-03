<?php

namespace Atarashii\APIBundle\Tests\Model;

use Atarashii\APIBundle\Model\Manga;

class MangaTest extends \PHPUnit_Framework_TestCase
{
    public function testId()
    {
        $mangaId = rand();

        $manga = new Manga();
        $manga->setId($mangaId);

        $this->assertEquals($mangaId, $manga->getId());
    }

    public function testTitle()
    {
        $mangaTitle = 'The Title';

        $manga = new Manga();
        $manga->setTitle($mangaTitle);

        $this->assertEquals($mangaTitle, $manga->getTitle());
    }

    public function testOtherTitles()
    {
        $manga = new Manga();

        $otherTitles = array();
        $otherTitles['english'][] = '5 Centimeters per Second';
        $otherTitles['synonyms'][] = 'Five Centimeters Per Second';
        $otherTitles['synonyms'][] = 'Byousoku 5 Centimeter - a chain of short stories about their distance';
        $otherTitles['synonyms'][] = '5 Centimetres Per Second';
        $otherTitles['synonyms'][] = '5 cm per second';
        $otherTitles['japanese'][] = '秒速５センチメートル';

        $manga->setOtherTitles($otherTitles);

        $this->assertEquals($otherTitles, $manga->getOtherTitles());
    }

    public function testRank()
    {
        $mangaRank = rand();

        $manga = new Manga();
        $manga->setRank($mangaRank);

        $this->assertEquals($mangaRank, $manga->getRank());
    }

    public function testPopularityRank()
    {
        $mangaRank = rand();

        $manga = new Manga();
        $manga->setPopularityRank($mangaRank);

        $this->assertEquals($mangaRank, $manga->getPopularityRank());
    }

    public function testImageUrl()
    {
        $mangaImgUrl = 'http://www.example.com/image.jpg';

        $manga = new Manga();
        $manga->setImageUrl($mangaImgUrl);

        $this->assertEquals($mangaImgUrl, $manga->getImageUrl());
    }

    public function testType()
    {
        $manga = new Manga();

        /*
         * Integers
         */
        $mangaType = 1;
        $manga->setType($mangaType);
        $this->assertEquals('Manga', $manga->getType());

        $mangaType = 2;
        $manga->setType($mangaType);
        $this->assertEquals('Novel', $manga->getType());

        $mangaType = 3;
        $manga->setType($mangaType);
        $this->assertEquals('One Shot', $manga->getType());

        $mangaType = 4;
        $manga->setType($mangaType);
        $this->assertEquals('Doujin', $manga->getType());

        $mangaType = 5;
        $manga->setType($mangaType);
        $this->assertEquals('Manwha', $manga->getType());

        $mangaType = 6;
        $manga->setType($mangaType);
        $this->assertEquals('Manhua', $manga->getType());

        $mangaType = 7;
        $manga->setType($mangaType);
        $this->assertEquals('OEL', $manga->getType());

        /*
         * Strings
         */
        $mangaType = 'Manga';
        $manga->setType($mangaType);
        $this->assertEquals($mangaType, $manga->getType());

        $mangaType = 'Novel';
        $manga->setType($mangaType);
        $this->assertEquals($mangaType, $manga->getType());

        $mangaType = 'One Shot';
        $manga->setType($mangaType);
        $this->assertEquals($mangaType, $manga->getType());

        $mangaType = 'Doujin';
        $manga->setType($mangaType);
        $this->assertEquals($mangaType, $manga->getType());

        $mangaType = 'Manwha';
        $manga->setType($mangaType);
        $this->assertEquals($mangaType, $manga->getType());

        $mangaType = 'Manhua';
        $manga->setType($mangaType);
        $this->assertEquals($mangaType, $manga->getType());

        $mangaType = 'OEL';
        $manga->setType($mangaType);
        $this->assertEquals($mangaType, $manga->getType());

        /*
         * Default Value
         */
        $mangaType = 'Invalid';
        $manga->setType($mangaType);
        $this->assertEquals('Manga', $manga->getType());
    }

    public function testChapters()
    {
        $chapters = rand();

        $manga = new Manga();
        $manga->setChapters($chapters);

        $this->assertEquals($chapters, $manga->getChapters());
    }

    public function testVolumes()
    {
        $volumes = rand();

        $manga = new Manga();
        $manga->setVolumes($volumes);

        $this->assertEquals($volumes, $manga->getVolumes());
    }

    public function testStatus()
    {
        $manga = new Manga();

        /*
         * Integers
         */
        $mangaStatus = 1;
        $manga->setStatus($mangaStatus);
        $this->assertEquals('publishing', $manga->getStatus());

        $mangaStatus = 2;
        $manga->setStatus($mangaStatus);
        $this->assertEquals('finished', $manga->getStatus());

        $mangaStatus = 3;
        $manga->setStatus($mangaStatus);
        $this->assertEquals('not yet published', $manga->getStatus());

        /*
         * Strings
         */
        $mangaStatus = 'publishing';
        $manga->setStatus($mangaStatus);
        $this->assertEquals($mangaStatus, $manga->getStatus());

        $mangaStatus = 'finished';
        $manga->setStatus($mangaStatus);
        $this->assertEquals($mangaStatus, $manga->getStatus());

        $mangaStatus = 'not yet published';
        $manga->setStatus($mangaStatus);
        $this->assertEquals($mangaStatus, $manga->getStatus());

        /*
         * Default Value
         */
        $mangaStatus = 'Invalid';
        $manga->setStatus($mangaStatus);
        $this->assertEquals('finished', $manga->getStatus());
    }

    public function testReadStatus()
    {
        $manga = new Manga();

        /*
         * Integers
         */
        $mangaReadStatus = 1;
        $manga->setReadStatus($mangaReadStatus);
        $this->assertEquals($mangaReadStatus, $manga->getReadStatus('int'));
        $this->assertEquals('reading', $manga->getReadStatus());

        $mangaReadStatus = 2;
        $manga->setReadStatus($mangaReadStatus);
        $this->assertEquals($mangaReadStatus, $manga->getReadStatus('int'));
        $this->assertEquals('completed', $manga->getReadStatus());

        $mangaReadStatus = 3;
        $manga->setReadStatus($mangaReadStatus);
        $this->assertEquals($mangaReadStatus, $manga->getReadStatus('int'));
        $this->assertEquals('on-hold', $manga->getReadStatus());

        $mangaReadStatus = 4;
        $manga->setReadStatus($mangaReadStatus);
        $this->assertEquals($mangaReadStatus, $manga->getReadStatus('int'));
        $this->assertEquals('dropped', $manga->getReadStatus());

        $mangaReadStatus = 6;
        $manga->setReadStatus($mangaReadStatus);
        $this->assertEquals($mangaReadStatus, $manga->getReadStatus('int'));
        $this->assertEquals('plan to read', $manga->getReadStatus());

        /*
         * Strings
         */
        $mangaReadStatus = 'reading';
        $manga->setReadStatus($mangaReadStatus);
        $this->assertEquals($mangaReadStatus, $manga->getReadStatus());

        $mangaReadStatus = 'completed';
        $manga->setReadStatus($mangaReadStatus);
        $this->assertEquals($mangaReadStatus, $manga->getReadStatus());

        $mangaReadStatus = 'on-hold';
        $manga->setReadStatus($mangaReadStatus);
        $this->assertEquals($mangaReadStatus, $manga->getReadStatus());

        $mangaReadStatus = 'onhold';
        $manga->setReadStatus($mangaReadStatus);
        $this->assertEquals('on-hold', $manga->getReadStatus());

        $mangaReadStatus = 'dropped';
        $manga->setReadStatus($mangaReadStatus);
        $this->assertEquals($mangaReadStatus, $manga->getReadStatus());

        $mangaReadStatus = 'plan to read';
        $manga->setReadStatus($mangaReadStatus);
        $this->assertEquals($mangaReadStatus, $manga->getReadStatus());

        $mangaReadStatus = 'plantoread';
        $manga->setReadStatus($mangaReadStatus);
        $this->assertEquals('plan to read', $manga->getReadStatus());

        /*
         * Default Value
         */
        $mangaReadStatus = 'Invalid';
        $manga->setReadStatus($mangaReadStatus);
        $this->assertEquals('reading', $manga->getReadStatus());
    }

    public function testPriority()
    {
        $manga = new Manga();

        /*
         * Integers
         */
        $mangaPriority = 0;
        $manga->setPriority($mangaPriority);
        $this->assertEquals('Low', $manga->getPriority('string'));
        $this->assertEquals($mangaPriority, $manga->getPriority());

        $mangaPriority = 1;
        $manga->setPriority($mangaPriority);
        $this->assertEquals('Medium', $manga->getPriority('string'));
        $this->assertEquals($mangaPriority, $manga->getPriority());

        $mangaPriority = 2;
        $manga->setPriority($mangaPriority);
        $this->assertEquals('High', $manga->getPriority('string'));
        $this->assertEquals($mangaPriority, $manga->getPriority());

        /*
         * Default Value
         */
        $mangaPriority = '27';
        $manga->setPriority($mangaPriority);
        $this->assertEquals('Low', $manga->getPriority('string'));
        $this->assertEquals('0', $manga->getPriority());
    }

    public function testMembersScore()
    {
        $score = 7.95;

        $manga = new Manga();
        $manga->setMembersScore($score);

        $this->assertEquals($score, $manga->getMembersScore());

        //Try inserting bad data
        $score = 'Fake';

        $manga->setMembersScore($score);

        $this->assertEquals((float) $score, $manga->getMembersScore());
    }

    public function testMembersCount()
    {
        $count = 238793;

        $manga = new Manga();
        $manga->setMembersCount($count);

        $this->assertEquals($count, $manga->getMembersCount());

        //Try inserting bad data
        $count = 'Fake';

        $manga->setMembersCount($count);

        $this->assertEquals((int) $count, $manga->getMembersCount());
    }

    public function testFavoritedCount()
    {
        $favs = 7295;

        $manga = new Manga();
        $manga->setFavoritedCount($favs);

        $this->assertEquals($favs, $manga->getFavoritedCount());
    }

    public function testSynopsis()
    {
        $synopsis = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce fringilla, nisi sed rutrum interdum, neque neque tincidunt eros, ac euismod enim massa a quam. Vivamus maximus enim ac odio euismod tristique. Suspendisse potenti. Praesent a nulla tortor. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aenean varius metus at sapien tempor auctor. Donec semper odio sed posuere placerat.';

        $manga = new Manga();
        $manga->setSynopsis($synopsis);

        $this->assertEquals($synopsis, $manga->getSynopsis());

        //Test unicode
        $synopsis = '漫研で夏コミへのサークル参加を決めたが……時間がない！　恋ヶ崎にムラサキさん、小豆ちゃんも手伝ってくれることになり、合宿を行うも——あれ、お酒で変なテンションになってない!?　……締切、間に合うよね？';

        $manga->setSynopsis($synopsis);

        $this->assertEquals($synopsis, $manga->getSynopsis());
    }

    public function testGenres()
    {
        $genres = 'Comedy, Parody, School, Slice of Life';

        $manga = new Manga();
        $manga->setGenres($genres);

        $this->assertEquals($genres, $manga->getGenres());
    }

    public function testAnimeAdaptations()
    {
        $relation = array();
        $relation['anime_id'] = rand();
        $relation['title'] = 'This is a title';
        $relation['url'] = 'http://myanimelist.net/anime/'.$relation['anime_id'];

        $manga = new Manga();
        $manga->setAnimeAdaptations($relation);

        $animeAdaptations = $manga->getAnimeAdaptations();

        $this->assertEquals($relation, $animeAdaptations[0]);
    }

    public function testRelatedManga()
    {
        $relation = array();
        $relation['manga_id'] = rand();
        $relation['title'] = 'This is a title';
        $relation['url'] = 'http://myanimelist.net/manga/'.$relation['manga_id'];

        $manga = new Manga();
        $manga->setRelatedManga($relation);

        $relatedManga = $manga->getRelatedManga();

        $this->assertEquals($relation, $relatedManga[0]);
    }

    public function testAlternativeVersions()
    {
        $relation = array();
        $relation['manga_id'] = rand();
        $relation['title'] = 'This is a title';
        $relation['url'] = 'http://myanimelist.net/manga/'.$relation['manga_id'];

        $manga = new Manga();
        $manga->setAlternativeVersions($relation);

        $altVersions = $manga->getAlternativeVersions();

        $this->assertEquals($relation, $altVersions[0]);
    }

    public function testChaptersRead()
    {
        $chapter = rand();

        $manga = new Manga();
        $manga->setChaptersRead($chapter);

        $this->assertEquals($chapter, $manga->getChaptersRead());
    }

    public function testVolumesRead()
    {
        $volume = rand();

        $manga = new Manga();
        $manga->setVolumesRead($volume);

        $this->assertEquals($volume, $manga->getVolumesRead());
    }

    public function testScore()
    {
        $score = rand(0, 10);

        $manga = new Manga();
        $manga->setScore($score);

        $this->assertEquals($score, $manga->getScore());
    }

    public function testListedMangaId()
    {
        $mangaId = rand();

        $manga = new Manga();
        $manga->setListedMangaId($mangaId);

        $this->assertEquals($mangaId, $manga->getListedMangaId());
    }

    public function testPersonalTags()
    {
        $tags = 'one, two, three, four';

        $manga = new Manga();
        $manga->setPersonalTags($tags);

        $this->assertEquals($tags, $manga->getPersonalTags());
    }

    public function testReadingStart()
    {
        $now = new \DateTime('now');
        $readDate = $now->format('Y-m-d');

        $manga = new Manga();
        $manga->setReadingStart($readDate);

        $this->assertEquals($readDate, $manga->getReadingStart());
    }

    public function testReadingEnd()
    {
        $now = new \DateTime('now');
        $readDate = $now->format('Y-m-d');

        $manga = new Manga();
        $manga->setReadingEnd($readDate);

        $this->assertEquals($readDate, $manga->getReadingEnd());
    }

    public function testChapDownloaded()
    {
        $chap = rand();

        $manga = new Manga();
        $manga->setChapDownloaded($chap);

        $this->assertEquals($chap, $manga->getChapDownloaded());
    }

    public function testRereading()
    {
        $manga = new Manga();

        //Using Real boolean
        $isReread = true;
        $manga->setRereading($isReread);
        $this->assertEquals($isReread, $manga->getRereading());

        //Using integers
        $isReread = 1;
        $manga->setRereading($isReread);
        $this->assertEquals(true, $manga->getRereading());

        //Invalid Data
        $isReread = 'Birdy';
        $manga->setRereading($isReread);
        $this->assertEquals((bool) $isReread, $manga->getRereading());
    }

    public function testRereadCount()
    {
        $count = rand();

        $manga = new Manga();
        $manga->setRereadCount($count);

        $this->assertEquals($count, $manga->getRereadCount());
    }

    public function testRereadValue()
    {
        $manga = new Manga();

        /*
         * Integers
         */
        $mangaRereadValue = 1;
        $manga->setRereadValue($mangaRereadValue);
        $this->assertEquals($mangaRereadValue, $manga->getRereadValue());
        $this->assertEquals('Very Low', $manga->getRereadValue('string'));

        $mangaRereadValue = 2;
        $manga->setRereadValue($mangaRereadValue);
        $this->assertEquals($mangaRereadValue, $manga->getRereadValue());
        $this->assertEquals('Low', $manga->getRereadValue('string'));

        $mangaRereadValue = 3;
        $manga->setRereadValue($mangaRereadValue);
        $this->assertEquals($mangaRereadValue, $manga->getRereadValue());
        $this->assertEquals('Medium', $manga->getRereadValue('string'));

        $mangaRereadValue = 4;
        $manga->setRereadValue($mangaRereadValue);
        $this->assertEquals($mangaRereadValue, $manga->getRereadValue());
        $this->assertEquals('High', $manga->getRereadValue('string'));

        $mangaRereadValue = 5;
        $manga->setRereadValue($mangaRereadValue);
        $this->assertEquals($mangaRereadValue, $manga->getRereadValue());
        $this->assertEquals('Very High', $manga->getRereadValue('string'));

        // Bad data
        $mangaRereadValue = 0;
        $manga->setRereadValue($mangaRereadValue);
        $this->assertEquals(null, $manga->getRereadValue());
        $this->assertEquals(null, $manga->getRereadValue('string'));

        $mangaRereadValue = 'Fake';
        $manga->setRereadValue($mangaRereadValue);
        $this->assertEquals(null, $manga->getRereadValue());
        $this->assertEquals(null, $manga->getRereadValue('string'));
    }

    public function testPersonalComments()
    {
        $comments = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce fringilla, nisi sed rutrum interdum, neque neque tincidunt eros, ac euismod enim massa a quam. Vivamus maximus enim ac odio euismod tristique. Suspendisse potenti. Praesent a nulla tortor. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aenean varius metus at sapien tempor auctor. Donec semper odio sed posuere placerat.';

        $manga = new Manga();
        $manga->setPersonalComments($comments);

        $this->assertEquals($comments, $manga->getPersonalComments());

        //Test unicode
        $comments = '漫研で夏コミへのサークル参加を決めたが……時間がない！　恋ヶ崎にムラサキさん、小豆ちゃんも手伝ってくれることになり、合宿を行うも——あれ、お酒で変なテンションになってない!?　……締切、間に合うよね？';

        $manga->setPersonalComments($comments);

        $this->assertEquals($comments, $manga->getPersonalComments());
    }

    //These functions being tested shouldn't be used anymore, MAL has phased the data out
    //Keeping tests in until they are removed from the model.

    public function testTags()
    {
        $manga = new Manga();

        $tags[] = 'one';
        $tags[] = 'two';
        $manga->setTags('one');
        $manga->setTags('two');

        $this->assertEquals($tags, $manga->getTags());
    }

    //It might be better to try and do an XML compare in the future.
    public function testMalXml()
    {
        $manga = new Manga();
        $items = array();

        $output = "<?xml version=\"1.0\"?>\n<entry><chapter>7</chapter><volume>7</volume><status>1</status><score>9</score><downloaded_chapters>8</downloaded_chapters><times_reread>1</times_reread><reread_value>4</reread_value><date_start>01012015</date_start><date_finish>01022015</date_finish><priority>0</priority><comments>This is a comment.</comments><tags>one,two,three</tags></entry>\n";

        $manga->setId(1);

        $manga->setChaptersRead(7);
        $items[] = 'chapters';

        $manga->setVolumesRead(7);
        $items[] = 'volumes';

        $manga->setReadStatus(1);
        $items[] = 'status';

        $manga->setScore(9);
        $items[] = 'score';

        $manga->setChapDownloaded(8);
        $items[] = 'downloaded';

        $manga->setRereadCount(1);
        $items[] = 'rereadCount';

        $manga->setRereadValue(4);
        $items[] = 'rereadValue';

        $manga->setReadingStart(new \DateTime('20150101'));
        $items[] = 'start';

        $manga->setReadingEnd(new \DateTime('20150102'));
        $items[] = 'end';

        $manga->setPriority(6);
        $items[] = 'priority';

        $manga->setPersonalComments('This is a comment.');
        $items[] = 'comments';

        $manga->setPersonalTags('one,two,three');
        $items[] = 'tags';

        $this->assertEquals($output, $manga->MALApiXml($items));
    }
}
