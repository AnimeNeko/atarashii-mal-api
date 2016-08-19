<?php
/**
 * Atarashii MAL API.
 *
 * @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
 * @author    Michael Johnson <youngmug@animeneko.net>
 * @copyright 2014-2016 Ratan Dhawtal and Michael Johnson
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
 */
namespace Atarashii\APIBundle\Parser;

use Symfony\Component\DomCrawler\Crawler;
use Atarashii\APIBundle\Model\Episode;
use DateTime;

class EpsParser
{
    public static function parse($contents)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $rows = $crawler->filter('table[class="mt8 episode_list js-watch-episode-list ascend"] tr[class="episode-list-data"]');

        $result = array();

        foreach ($rows as $episodeItem) {
            $crawler = new Crawler($episodeItem);
            $episode = new Episode();
            $episode->setNumber($crawler->filter('td[class="episode-number nowrap"]')->text());
            $episode->setTitle($crawler->filter('td[class="episode-title"] a')->text());

            // MAL does not always provide the air date!
            $date = $crawler->filter('td[class="episode-aired"]')->text();
            if ($date !== 'N/A') {
                $dateTime = new DateTime();
                $episode->setAirDate($dateTime->createFromFormat('M j, Y', $date));
            }

            $extracted = $crawler->filter('td[class="episode-title"] span[class="di-ib"]');
            if ($extracted->text() !== '' && $extracted->count() > 0) {

                # English:
                $extracted = explode('(', $extracted->text());
                if (count($extracted) > 0) {
                    $other_titles['english'] = array(trim($extracted[0], chr(0xC2).chr(0xA0)));
                }

                # Japanese:
                if (count($extracted) > 1) {
                    $other_titles['japanese'] = array(trim(str_replace(')', '', $extracted[1])));
                }
                $episode->setOtherTitles($other_titles);
            }

            $result[] = $episode;
        }

        return $result;
    }
}
