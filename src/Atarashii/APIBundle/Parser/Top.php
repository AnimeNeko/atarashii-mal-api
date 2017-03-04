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
use Atarashii\APIBundle\Model\Anime;
use Atarashii\APIBundle\Model\Manga;

class Top
{
    public static function parse($contents, $type)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        //Filter into a set of tds from the source HTML table
        $mediaitems = $crawler->filter('tr[class="ranking-list"]');

        foreach ($mediaitems as $item) {
            $resultset[] = self::parseRecord($item, $type);
        }

        return $resultset;
    }

    private static function parseRecord($item, $type)
    {
        $crawler = new Crawler($item);

        //Initialize our object based on the record type we were passed.
        switch ($type) {
            case 'anime':
                $media = new Anime();
                break;
            case 'manga':
                $media = new Manga();
                break;
        }

        //Separate all the details
        $details = explode("\n", trim($crawler->filter('div[class="detail"]')->text()));
        $subDetails = explode(' ', trim($details[1]));

        //Pull out all the common parts
        $media->setId((int)str_replace('#area', '', $crawler->filter('a')->attr('id')));
        $media->setTitle($crawler->filter('a')->eq(1)->text());
        //Convert thumbnail to full size image by stripping the "t" in the filename
        $media->setImageUrl(preg_replace('/r(.+?)\/(.+?)\?(.+?)$/', '$2', $crawler->filter('img')->attr('data-src')));
        $media->setMembersCount((int)trim(str_replace(',', '', str_replace('members', '', $details[3]))));

        //Anime and manga have different details, so we grab an array of the list and then process based on the type
        switch ($type) {
            case 'anime':
                $media->setType($subDetails[0]);
                $media->setEpisodes(strstr($subDetails[1], '?') ? null : (int) trim(str_replace('eps', '', $subDetails[1]), '()'));
                $media->setMembersScore((float) $crawler->filter('td')->eq(2)->text());
                break;
            case 'manga':
                $media->setVolumes(strstr($subDetails[1], '?') ? null : (int) trim(str_replace('vols', '', $subDetails[1]), '()'));
                $media->setMembersScore((float) $crawler->filter('td')->eq(2)->text());
                break;
        }

        return $media;
    }
}
