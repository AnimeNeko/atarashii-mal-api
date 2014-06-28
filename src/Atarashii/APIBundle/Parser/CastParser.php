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
use Atarashii\APIBundle\Model\Cast;

class CastParser
{
    public static function parse($contents)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $items = $crawler->filter('div[style="padding: 0 7px;"] table[width="100%"]');

        //Bypass Undefined variable error.
        $staff = null;
        $characters = null;

        foreach ($items as $item) {
            //Bypass to determine if the last table contains the staff members
            $crawler = new Crawler($item);
            if ($crawler->filter('tr')->eq(0)->filter('td[width="25"]')->count() == 1) {
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

        $cast->setName($item->filter('a')->eq(1)->text());
        $cast->setImage(str_replace('t.jpg', '.jpg', $item->filter('img')->attr('src')));
        $cast->setRole($item->filter('small')->text());
        if ($item->filter('td table')->count() > 0) {
            $cast->setActorName($item->filter('td')->eq(2)->filter('a')->text());
            $cast->setActorLanguage($item->filter('small')->last()->text());
            $cast->setActorImage(str_replace('v.jpg', '.jpg', $item->filter('img')->last()->attr('src')));
        }

        return $cast;
    }

    private static function parseStaff($item)
    {
        $crawler = new Crawler($item);
        $cast = new Cast();

        $cast->setName($crawler->filter('a')->eq(1)->text());
        $cast->setRank($crawler->filter('small')->last()->text());
        $cast->setImage(str_replace('v.jpg', '.jpg', $crawler->filter('img')->last()->attr('src')));

        return $cast;
    }
}
