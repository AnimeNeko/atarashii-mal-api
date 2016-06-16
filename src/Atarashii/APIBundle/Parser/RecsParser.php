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

class RecsParser
{
    public static function parse($contents)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $rows = $crawler->filter('div[class="borderClass"]');

        $result = array();


        foreach ($rows as $historyItem) {
            $crawler = new Crawler($historyItem);
            $anime = new Anime();
            $anime->setId(str_replace('#raArea1', '', $crawler->filter('a')->attr('id')));
            $anime->setImageUrl(preg_replace('/r(.+?)\/(.+?)\?(.+?)$/', '$2', $crawler->filter('img')->attr('data-src')));
            $anime->setTitle($crawler->filter('strong')->text());
            $resultItem['item'] = $anime;
            $resultItem['recommendations'] = self::parseInformation($crawler);
            $result[] = $resultItem;
        }

        return $result;
    }

    public static function parseInformation($crawler)
    {
        $rows = $crawler->filter('div[style="padding: 3px; border-width: 1px 0 0 0; margin: 4px 0;"]');
        $result = array();

        foreach ($rows as $historyItem) {
            $crawler = new Crawler($historyItem);
            $resultItem['information'] = str_replace('&nbspread more', '', $crawler->filter('div[class="spaceit_pad"]')->text());
            $resultItem['username'] = $crawler->filter('div[class="spaceit_pad"]')->eq(1)->filter('a')->eq(1)->text();
            $result[] = $resultItem;
        }

        return $result;
    }
}
