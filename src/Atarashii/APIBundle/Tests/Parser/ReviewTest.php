<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Parser\ReviewParser;

class ReviewTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $reviewContents = file_get_contents(__DIR__.'/../InputSamples/anime-1887-reviews.html');

        $reviews = ReviewParser::parse($reviewContents, 'anime');

        $this->assertInternalType('array', $reviews);

        $review = $reviews[0];

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Review', $review);
        $this->assertInstanceOf('\DateTime', new \DateTime($review->getDate()));

        $this->assertInternalType('int', $review->getRating());
        $this->assertGreaterThanOrEqual('1', $review->getRating());
        $this->assertLessThanOrEqual('10', $review->getRating());

        $this->assertInternalType('string', $review->getUsername());
        $this->assertInternalType('int', $review->getHelpful());

        $this->assertInternalType('int', $review->getEpisodes());
        $this->assertNull($review->getChapters());

        $reviewContents = file_get_contents(__DIR__.'/../InputSamples/manga-11977-reviews.html');

        $reviews = ReviewParser::parse($reviewContents, 'M');

        $this->assertInternalType('array', $reviews);

        $review = $reviews[0];

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Review', $review);
        $this->assertInstanceOf('\DateTime', new \DateTime($review->getDate()));

        $this->assertInternalType('int', $review->getRating());
        $this->assertGreaterThanOrEqual('1', $review->getRating());
        $this->assertLessThanOrEqual('10', $review->getRating());

        $this->assertInternalType('string', $review->getUsername());
        $this->assertInternalType('int', $review->getHelpful());

        $this->assertInternalType('int', $review->getChapters());
        $this->assertNull($review->getEpisodes());
    }
}
