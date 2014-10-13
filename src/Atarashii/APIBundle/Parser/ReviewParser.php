<?php
/**
* Atarashii MAL API
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/

namespace Atarashii\APIBundle\Parser;

use Symfony\Component\DomCrawler\Crawler;
use Atarashii\APIBundle\Model\Review;
use \DateTime;

class ReviewParser
{
    public static function parse($contents, $type)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $items = $crawler->filter('div [class="borderDark"]');
        foreach ($items as $item) {
            $result[] = self::parseReviews($item, $type);
        }

        return $result;
    }

    private static function parseReviews($item, $type)
    {
        $crawler = new Crawler($item);
        $review = new Review();

        $episodes = explode(' ', $crawler->filter('td[style="text-align: right;"] div')->eq(1)->text());

        //The review is not clean and should be separated from the other html codes
        $firstpart = explode('div>', $crawler->filter('div[class="spaceit textReadability"]')->html());
        $secondpart = explode('</span>', $firstpart[1]);

        $review->setDate($crawler->filter('div[class="spaceit"] div')->text());
        $review->setRating(str_replace('Overall Rating: ', '', $crawler->filter('tr td[style="text-align: right;"] div')->last()->text()));
        $review->setAvatarUrl(str_replace('_thumb','' , str_replace('/thumbs', '', $crawler->filter('div[class="picSurround"] img')->attr('src'))));
        $review->setUsername($crawler->filter('tr a')->eq(2)->text());
        $review->setHelpful($crawler->filter('tr strong')->text());
        $review->setHelpfulTotal($crawler->filter('tr strong')->eq(1)->text());
        $review->setReview($secondpart[0]);

        if ($type == 'A'){
            if (count($episodes) >= 3) {
               $review->setEpisodes($episodes[2]);
            }
            $review->setWatchedEpisodes($episodes[0]);
        } else {
            if (count($episodes) >= 3) {
                $review->setChapters($episodes[2]);
            }
            $review->setChaptersRead($episodes[0]);
        }

        return $review;

    }
}
