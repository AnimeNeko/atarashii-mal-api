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
            //Bypass to determine if the last table contains the staff members
            $crawler = new Crawler($item);
            if ($crawler->filter('td[width="27"]')->count() != 1) {
                $staffitems = $crawler->children();
                foreach ($staffitems as $staffitem) {
                    $staff[] = self::parseStaff($staffitem);
                }
            } else {
                $characters[] = self::parseCharacters($crawler);
            }
        }

        return array('Characters' => $characters, 'Staff' => $staff);
    }

    private static function parseCharacters($item)
    {
        $cast = new Cast();

        $cast->setId(explode('/', $item->filter('a')->attr('href'))[2]);
        $cast->setName($item->filter('a')->eq(1)->text());
        $cast->setImage(str_replace('t.jpg', '.jpg', $item->filter('img')->attr('src')));
        $cast->setRole($item->filter('small')->text());

            foreach ($item->filter('table[class="space_table"] tr') as $actorItem) {
                $actor = new Actor();
                $crawler = new Crawler($actorItem);

                if ($crawler->filter('td')->count() > 1) {
                    $actor->setId(explode('/', $crawler->filter('a')->attr('href'))[2]);
                    $actor->setName($crawler->filter('a')->text());
                    $actor->setLanguage($crawler->filter('small')->last()->text());
                    $actor->setImage(str_replace('v.jpg', '.jpg', $crawler->filter('img')->last()->attr('src')));

                    $cast->setActors($actor);
                }
        }

        return $cast;
    }

    private static function parseStaff($item)
    {
        $crawler = new Crawler($item);
        $cast = new Cast();

        $cast->setId(explode('/', $crawler->filter('a')->attr('href'))[2]);
        $cast->setName($crawler->filter('a')->eq(1)->text());
        $cast->setRank($crawler->filter('small')->last()->text());
        $cast->setImage(str_replace('v.jpg', '.jpg', $crawler->filter('img')->last()->attr('src')));

        return $cast;
    }
}
