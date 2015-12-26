<?php
/**
* Atarashii MAL API
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014-2015 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/

namespace Atarashii\APIBundle\Parser;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Serializer\Serializer;
use Atarashii\APIBundle\Model\Anime;
use Atarashii\APIBundle\Model\Manga;
use \DateTime;

class Upcoming
{
    public static function parse($contents,$type)
    {
        $resultset = '';

        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');
        $menubar = true;

        //Filter into a set of tds from the source HTML table
        $mediaitems = $crawler->filterXPath('//div[@id="content"]/table/tr');

        foreach ($mediaitems as $item) {
            //tricky method to skip the menu bar which is also a <tr></tr>
            if ($menubar == true) {
                $menubar = false;
            } else {
                $resultset[] = self::parseRecord($item, $type);
            }
        }

        return $resultset;
    }

    private static function parserecord($item,$type)
    {
        $crawler = new Crawler($item);
        $check = true;

        //Get the type record.
        switch ($type) {
            case 'anime':
                $media = new Anime();
                break;
            case 'manga':
                $media = new Manga();
                break;
        }

        //Pull out all the common parts
        $media->setId((int) str_replace('#sarea', '', $crawler->filter('a')->attr('id')));
        $media->setTitle($crawler->filter('strong')->text());

        //Title Image
        //We need to do some string manipulation here so it doesn't return a tiny image
        $imageUrl = $crawler->filter('img')->attr('src');
        $imageUrl = preg_replace('/\?s.*$/', '', $imageUrl); //Remove session info
        $imageUrl = preg_replace('/r\/[0-9]*?x[0-9]*?\//', '', $imageUrl); //Remove the resize part of the path
        $media->setImageUrl($imageUrl);

        $media->setType(trim($crawler->filterXPath('//td[3]')->text()));

        switch ($type) {
            case 'anime':
                //Custom parsing for anime
                $media->setEpisodes((int) trim($crawler->filterXPath('//td[4]')->text()));

                $start_date = trim($crawler->filterXPath('//td[6]')->text());

                if ($start_date != '-') {
                    $start_date = explode('-', trim($start_date));

                    if (strlen($start_date[2]) == 2) {
                        $start_date[2] = self::fixMalShortYear($start_date[2]);
                    }

                    // If we don't know the month, then we can only be accurate to a year.
                    if ($start_date[0] == '?') {
                        $media->setLiteralStartDate(null, DateTime::createFromFormat('Y', $start_date[2]), 'year');
                    } elseif ($start_date[0] != '?' && $start_date[1] == '?') {
                        $media->setLiteralStartDate(null, DateTime::createFromFormat('Y m', "$start_date[2] $start_date[0]"), 'month');
                    } else {
                        $media->setLiteralStartDate("$start_date[2]-$start_date[0]-$start_date[1]", DateTime::createFromFormat('Y m d', "$start_date[2] $start_date[0] $start_date[1]"), 'day');
                    }
                }

                $end_date = trim($crawler->filterXPath('//td[7]')->text());

                if ($end_date != '-') {
                    $end_date = explode('-', trim($end_date));

                    if (strlen($end_date[2]) == 2) {
                        $end_date[2] = self::fixMalShortYear($end_date[2]);
                    }

                    if ($end_date[0] == '?') {
                        $media->setLiteralEndDate(null, DateTime::createFromFormat('Y', $end_date[2]), 'year');
                    } elseif ($end_date[0] != '?' && $end_date[1] == '?') {
                        $media->setLiteralEndDate(null, DateTime::createFromFormat('Y m', "$end_date[2] $end_date[0]"), 'month');
                    } else {
                        $media->setLiteralEndDate("$end_date[2]-$end_date[0]-$end_date[1]", DateTime::createFromFormat('Y m d', "$end_date[2] $end_date[0] $end_date[1]"), 'day');
                    }
                }

                $media->setClassification(trim($crawler->filterXPath('//td[9]')->text()));
                $media->setMembersScore((float) trim($crawler->filterXPath('//td[5]')->text()));
                $media->setSynopsis(trim($crawler->filterXPath('//td[2]/div[3]')->text()));
                break;
            case 'manga':
                //Custom parsing for manga
                $media->setType(trim($crawler->filterXPath('//td[3]')->text()));
                $media->setChapters((int) trim($crawler->filterXPath('//td[5]')->text()));
                $media->setVolumes((int) trim($crawler->filterXPath('//td[4]')->text()));
                $media->setMembersScore((float) trim($crawler->filterXPath('//td[6]')->text()));
                $media->setSynopsis(trim($crawler->filterXPath('//td[2]/div[2]')->text()));
                break;
            }

        return $media;
    }

    private static function fixMalShortYear($year) {
        //Create a four digit year from MAL's display.
        //We can't use PHP's built-in date parser as it parses two-digit years
        //in the range 1970-2069. We need earlier, so have to do it manually.
        //We use the range 1930-2029, which will create some incorrect dates
        //for titles from the early part of the 20th century, but it's the best
        //fix at this point.
        if($year >= 30) {
            return '19' . $year;
        } else {
            return '20' . $year;
        }
    }
}
