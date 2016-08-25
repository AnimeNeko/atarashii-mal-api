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

        foreach ($item->filter('table[class="space_table"] tr') as $actorItem) {
            $actor = new Actor();
            $crawler = new Crawler($actorItem);

            if ($crawler->filter('td')->count() > 1) {
                if (preg_match('/people\/(.*?)\/.*$/', $crawler->filter('a')->attr('href'), $actorIds)) {
                    $actor->setId($actorIds[1]);
                }

                $actor->setName($crawler->filter('a')->text());
                $actor->setLanguage($crawler->filter('small')->last()->text());

                $imageUrl = $item->filter('img')->last()->attr('data-src');
                $imageUrl = preg_replace('/\/r\/.*?x.*?\//', '/', $imageUrl);
                $imageUrl = preg_replace('/\?s=.*$/', '', $imageUrl);
                $actor->setImage($imageUrl);

                $cast->setActors($actor);
            }
        }

        return $cast;
    }

    private static function parseStaff($item)
    {
        $crawler = new Crawler($item);
        $cast = new Cast();

        if (preg_match('/people\/(.*?)\/.*$/', $crawler->filter('a')->attr('href'), $castId)) {
            $cast->setId($castId[1]);
        }

        $cast->setName($crawler->filter('a')->eq(1)->text());
        $cast->setRank($crawler->filter('small')->last()->text());

        $imageUrl = $crawler->filter('img')->last()->attr('data-src');
        $imageUrl = preg_replace('/\/r\/.*?x.*?\//', '/', $imageUrl);
        $imageUrl = preg_replace('/\?s=.*$/', '', $imageUrl);
        $cast->setImage($imageUrl);

        return $cast;
    }
}
