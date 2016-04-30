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
use Atarashii\APIBundle\Model\Anime;
use Atarashii\APIBundle\Model\Manga;
use Atarashii\APIBundle\Helper\Date;

class HistoryParser
{
    public static function parse($contents, $id, $type)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $rows = $crawler->filter('div[class="spaceit_pad"]');
        $title = preg_replace('/ (\w+?) Details/', '$2', $crawler->filter('div[class="normal_header"]')->text());

        $result = array();

        if ($type === 'anime') {
            foreach ($rows as $historyItem) {
                $crawler = new Crawler($historyItem);
                $date = explode(' ', $crawler->text());
                $historyinfo['item'] = new Anime();
                $historyinfo['item']->setId((int) $id);
                $historyinfo['item']->setTitle($title);
                $historyinfo['item']->setWatchedEpisodes((int) $date[1]);
                $historyinfo['type'] = $type;
                $historyinfo['time_updated'] = Date::formatTime($date[4].' '.$date[6]);
                $result[] = $historyinfo;
            }
        } else {
            foreach ($rows as $historyItem) {
                $crawler = new Crawler($historyItem);
                $date = explode(' ', $crawler->text());
                $historyinfo['item'] = new Manga();
                $historyinfo['item']->setId((int) $id);
                $historyinfo['item']->setTitle($title);
                $historyinfo['item']->setChaptersRead((int) $date[1]);
                $historyinfo['type'] = $type;
                $historyinfo['time_updated'] = Date::formatTime($date[4].' '.$date[6]);
                $result[] = $historyinfo;
            }
        }

        return $result;
    }
}
