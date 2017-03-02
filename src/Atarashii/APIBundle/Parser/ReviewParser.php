<?php
/**
 * Atarashii MAL API.
 *
 * @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
 * @author    Michael Johnson <youngmug@animeneko.net>
 * @copyright 2014-2015 Ratan Dhawtal and Michael Johnson
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
 */
namespace Atarashii\APIBundle\Parser;

use Symfony\Component\DomCrawler\Crawler;
use Atarashii\APIBundle\Model\Review;

class ReviewParser
{
    public static function parse($contents, $type)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $result = array();

        $items = $crawler->filterXPath('//div[@id="content"]//table//div[@class="js-scrollfix-bottom-rel"]//div[@class="borderDark"]');
        foreach ($items as $item) {
            $result[] = self::parseReviews($item, $type);
        }

        return $result;
    }

    private static function parseReviews($item, $type)
    {
        $crawler = new Crawler($item);
        $review = new Review();

        $avatar = $crawler->filterXPath('//td[1]//img');

        if ($avatar->count() > 0) {
            $avatar = $avatar->attr('data-src');
            $avatar = str_replace('_thumb', '', $avatar);
            $avatar = str_replace('/thumbs', '', $avatar);

            $review->setAvatarUrl($avatar);
        }

        $review->setUsername($crawler->filterXPath('//td[2]/a')->text());
        $review->setHelpful($crawler->filterXPath('//td[2]/div//span')->text());
        $review->setHelpfulTotal($crawler->filterXPath('//td[2]/div//span')->text()); //Set to same as helpful for now, as the total votes are removed.

        //Details Box
        $details = $crawler->filterXPath('//div[@class="mb8"]');

        //Progress
        $progress = $details->filterXPath('//div[contains(@class, "lightLink")]')->text();

        if (preg_match('/(\d+) of (\d+|\?)/', $progress, $matches)) {
            if ($type === 'anime') {
                $review->setWatchedEpisodes((int) $matches[1]);

                if ($matches[2] != '?') {
                    $review->setEpisodes((int) $matches[2]);
                }
            } else {
                $review->setChaptersRead((int) $matches[1]);

                if ($matches[2] != '?') {
                    $review->setChapters((int) $matches[2]);
                }
            }
        }

        //Rating
        $rating = $details->filterXPath('//div/a');
        $ratingText = str_replace($rating->text(), '', $rating->parents()->text());

        if (preg_match('/(\d+)/', $ratingText, $matches)) {
            $review->setRating((int) $matches[1]);
        }

        //Date
        $date = trim(str_replace($rating->parents()->text(), '', str_replace($progress, '', $details->text())));
        $review->setDate($date);

        //Review Body
        $reviewBody = $crawler->filterXPath('//div[contains(@class,"textReadability")]');

        //The review text is split up a bit, and has some hidden data, so we will need to clean things up.
        $reviewBlock = trim($reviewBody->html());

        //Select which expression to use for stripping text.
        //Some longer reviews will have a "read more" link, others are short and do not.
        if (strpos($reviewBlock, 'reviewToggle') === false) {
            //Short Review, no hidden text
            preg_match('/<div .*?>.*?<\/div>(.*)/s', $reviewBlock, $reviewParts);
            $reviewText = $reviewParts[1];
        } else {
            //Long review, split text, second half hidden
            preg_match('/<div .*?>.*?<\/div>(.*?)<span .*?>(.*?)<\/span>/s', $reviewBlock, $reviewParts);
            $reviewText = $reviewParts[1].$reviewParts[2];
        }

        //Remove ending div and link, if present, from the review body
        if(strpos($reviewText, 'Helpful</a>') !== false) {
            if(preg_match('/(.*?)<div .*?>.*?<\/div>/s', $reviewText, $reviewMain)) {
                $reviewText = $reviewMain[1];
            }
        }

        $review->setReview(trim($reviewText));

        return $review;
    }
}
