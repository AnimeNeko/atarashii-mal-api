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

use Atarashii\APIBundle\Model\Actor;
use Symfony\Component\DomCrawler\Crawler;
use Atarashii\APIBundle\Model\Cast;

class CastParser
{
    public static function parse($contents)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $items = $crawler->filter('td[style="padding-left: 5px;"] table[width="100%"]');

        //Bypass Undefined variable error.
        $staff = null;
        $characters = null;

        foreach ($items as $item) {
            $crawler = new Crawler($item);

            //MAL doesn't easily separate the character and staff information.
            //This is a dirty hack to tell if it's a character or staff

            $url = $crawler->filterXPath('//td/div[@class="picSurround"]/a')->attr('href');
            if (stristr($url, 'character')) {
                $characters[] = self::parseCharacters($crawler);
            } else {
                $staff[] = self::parseStaff($crawler);
            }
        }

        return array('Characters' => $characters, 'Staff' => $staff);
    }

    private static function parseCharacters(Crawler $item)
    {
        $cast = new Cast();

        if (preg_match('/character\/(.*?)\/.*$/', $item->filter('a')->attr('href'), $characterIds)) {
            $cast->setId($characterIds[1]);
        }

        $cast->setName($item->filter('a')->eq(1)->text());

        $imageUrl = $item->filter('img')->attr('data-src');
        $imageUrl = preg_replace('/\/r\/.*?x.*?\//', '/', $imageUrl);
        $imageUrl = preg_replace('/\?s=.*$/', '', $imageUrl);
        $cast->setImage($imageUrl);

        $cast->setRole($item->filter('small')->text());

        foreach ($item->filter('td[align="right"] tr') as $actorItem) {
            $actor = new Actor();
            $crawler = new Crawler($actorItem);

            if ($crawler->filter('td')->count() > 1) {
                if (preg_match('/people\/(.*?)\/.*$/', $crawler->filter('a')->attr('href'), $actorIds)) {
                    $actor->setId($actorIds[1]);
                }

                $actor->setName($crawler->filter('a')->text());
                $actor->setLanguage($crawler->filter('small')->last()->text());

                $imageUrl = $crawler->filter('img')->last()->attr('data-src');
                $imageUrl = preg_replace('/\/r\/.*?x.*?\//', '/', $imageUrl);
                $imageUrl = preg_replace('/\?s=.*$/', '', $imageUrl);
                $actor->setImage($imageUrl);

                $cast->setActors($actor);
            }
        }

        return $cast;
    }

    private static function parseStaff(Crawler $item)
    {
        $cast = new Cast();

        $url = $item->filterXPath('//td[2]/a');

        if (preg_match('/people\/(.*?)\/.*$/', $url->attr('href'), $castId)) {
            $cast->setId($castId[1]);
        }

        $cast->setName($item->filter('a')->eq(1)->text());
        $cast->setRank($item->filter('small')->last()->text());

        $imageUrl = $item->filter('img')->last()->attr('data-src');
        $imageUrl = preg_replace('/\/r\/.*?x.*?\//', '/', $imageUrl);
        $imageUrl = preg_replace('/\?s=.*$/', '', $imageUrl);
        $cast->setImage($imageUrl);

        return $cast;
    }
}
