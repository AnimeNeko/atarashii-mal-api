<?php

namespace Atarashii\APIBundle\Tests\Episode;

use Atarashii\APIBundle\Model\Episode;

class EpisodeTest extends \PHPUnit_Framework_TestCase
{
    public function testNumber()
    {
        $epNum = rand(5, 12);

        $episode = new Episode();
        $episode->setNumber($epNum);

        $this->assertEquals($epNum, $episode->getNumber());
    }

    public function testTitle()
    {
        $title = 'Test Title';

        $episode = new Episode();
        $episode->setTitle($title);

        $this->assertEquals($title, $episode->getTitle());
    }

    public function testOtherTitles()
    {
        $titles = array();
        $titles[] = 'Alternate 1';
        $titles[] = 'Second Alt';

        $episode = new Episode();
        $episode->setOtherTitles($titles);

        $this->assertEquals($titles, $episode->getOtherTitles());
    }

    public function testAirDate()
    {
        $airDate = new \DateTime();
        $episode = new Episode();

        // Day Accuracy
        $expected = $airDate->format('Y-m-d');
        $episode->setAirDate($airDate, 'day');
        $this->assertEquals($expected, $episode->getAirDate());

        // Month Accuracy
        $expected = $airDate->format('Y-m');
        $episode->setAirDate($airDate, 'month');
        $this->assertEquals($expected, $episode->getAirDate());

        // Year Accuracy
        $expected = $airDate->format('Y');
        $episode->setAirDate($airDate, 'year');
        $this->assertEquals($expected, $episode->getAirDate());
    }
}
