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
use Symfony\Component\Serializer\Serializer;
use Atarashii\APIBundle\Model\Anime;
use Atarashii\APIBundle\Model\Manga;
use \DateTime;

class Upcoming
{
    public static function parse($contents,$type)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');
        $menubar = true;

        //Filter into a set of tds from the source HTML table
        $mediaitems = $crawler->filter('#horiznav_nav')->nextAll()->filterXPath('./div/table/tr');

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

        //I removed the 't' because it will else return a little image
        $media->setImageUrl(str_replace('t.j', '.j', $crawler->filter('img')->attr('src')));
        $media->setType(trim($crawler->filterXPath('//td[3]')->text()));

        switch ($type) {
            case 'anime':
                //Custom parsing for anime
                $media->setEpisodes((int) trim($crawler->filterXPath('//td[4]')->text()));

                $start_date = trim($crawler->filterXPath('//td[6]')->text());

                if (strpos($start_date, '?') === false && $start_date !== '-') {
                    $start_date = DateTime::createFromFormat('m-d-y', $start_date)->format('Y-m-d');
                }
                $media->setStartDate($start_date);

                $end_date = trim($crawler->filterXPath('//td[7]')->text());
                if (strpos($end_date, '?') === false && $end_date !== '-') {
                    $end_date = DateTime::createFromFormat('m-d-y', $end_date)->format('Y-m-d');
                }

                $media->setEndDate($end_date);
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
}
