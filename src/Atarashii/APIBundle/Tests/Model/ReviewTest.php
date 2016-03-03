<?php

namespace Atarashii\APIBundle\Tests\Model;

use Atarashii\APIBundle\Model\Review;

class ReviewTest extends \PHPUnit_Framework_TestCase
{
    public function testAvatarUrl()
    {
        $avaUrl = 'http://www.example.com/image.jpg';

        $review = new Review();
        $review->setAvatarUrl($avaUrl);

        $this->assertEquals($avaUrl, $review->getAvatarUrl());
    }

    public function testChapters()
    {
        $chap = rand();

        $review = new Review();
        $review->setChapters($chap);

        $this->assertEquals($chap, $review->getChapters());
    }

    public function testChaptersRead()
    {
        $chap = rand();

        $review = new Review();
        $review->setChaptersRead($chap);

        $this->assertEquals($chap, $review->getChaptersRead());
    }

    public function testDate()
    {
        $date = 'Jan 25, 2010 7:34 AM';
        $expected = '2010-01-25T07:34-0800';

        $review = new Review();
        $review->setDate($date);

        $this->assertEquals($expected, $review->getDate());
    }

    public function testEpisodes()
    {
        $eps = rand();

        $review = new Review();
        $review->setEpisodes($eps);

        $this->assertEquals($eps, $review->getEpisodes());
    }

    public function testWatchedEpisodes()
    {
        $eps = rand();

        $review = new Review();
        $review->setWatchedEpisodes($eps);

        $this->assertEquals($eps, $review->getWatchedEpisodes());
    }

    public function testHelpful()
    {
        $helped = rand();

        $review = new Review();
        $review->setHelpful($helped);

        $this->assertEquals($helped, $review->getHelpful());
    }

    public function testHelpfulTotal()
    {
        $helped = rand();

        $review = new Review();
        $review->setHelpfulTotal($helped);

        $this->assertEquals($helped, $review->getHelpfulTotal());
    }

    public function testRating()
    {
        $rating = rand(1, 10);

        $review = new Review();
        $review->setRating($rating);

        $this->assertEquals($rating, $review->getRating());
    }

    public function testReview()
    {
        $reviewText = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean molestie nisi sed lacus tincidunt, vel vestibulum enim facilisis. Nunc consectetur eget justo placerat scelerisque. Nullam eu augue ullamcorper, faucibus elit sed, malesuada libero. Proin pretium elit quis arcu eleifend venenatis. Vestibulum efficitur cursus tellus eu pharetra. Cras pharetra accumsan consequat.';

        $review = new Review();
        $review->setReview($reviewText);

        $this->assertEquals($reviewText, $review->getReview());
    }

    public function testUsername()
    {
        $username = 'TaroYamada';

        $review = new Review();
        $review->setUsername($username);

        $this->assertEquals($username, $review->getUsername());
    }
}
