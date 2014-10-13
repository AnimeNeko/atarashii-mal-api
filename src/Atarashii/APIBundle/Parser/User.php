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
use Atarashii\APIBundle\Model\Profile;
use Atarashii\APIBundle\Model\Date;
use Atarashii\APIBundle\Model\Anime;
use Atarashii\APIBundle\Model\Manga;

class User
{
    public static function parse($contents)
    {
        $user = new Profile();

        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $leftside = $crawler->filter('#content .profile_leftcell');

        $user->setAvatarUrl($leftside->filter('img')->attr('src'));

        $maincontent = iterator_to_array($crawler->filter('#horiznav_nav')->nextAll()->filterXPath('./div/table/tr/td'));

        $userdetails = $maincontent[0];
        $animestats = $maincontent[2];
        $mangastats = $maincontent[3];

        $user->details = self::parseDetails($userdetails, $user->details); //Details is an object, so we need to pass it to the function.
        $user->anime_stats = self::parseStats($animestats, $user->anime_stats);
        $user->manga_stats = self::parseStats($mangastats, $user->manga_stats);

        return $user;
    }

    private static function parseDetails($content, $details)
    {
        $elements = new Crawler($content);
        $elements = $elements->filter('tr');

        foreach ($elements as $content) {

            $crawler = new Crawler($content);
            $crawler = $crawler->filter('td');

            $values = iterator_to_array($crawler);

            $key = 'set'.trim(str_replace(' ', '', $values[0]->textContent));
            $value = trim($values[1]->textContent);

            //We have to do some casting and manipulation for certain values so we return them as the right type
            switch (strtolower(str_replace('set', '', $key))) {
                case 'forumposts':
                case 'mangalistviews':
                case 'animelistviews':
                case 'comments':
                    $value = (int) str_replace(',', '', $value);
                    break;
                case 'website':
                    //Display value is truncated if it's too long, so get the href value instead.
                    $value = $values[1]->firstChild->getAttribute('href');
                    break;
            }
            $details->$key($value);
        }

        return $details;
    }

    private static function parseStats($content, $stats)
    {
        $elements = new Crawler($content);
        $elements = $elements->filter('tr');

        foreach ($elements as $content) {

            $crawler = new Crawler($content);
            $crawler = $crawler->filter('td');

            $values = iterator_to_array($crawler);
            $value = trim($values[1]->textContent);

            //Some of the key values have parenthesis. This is messy, but we need to
            //extract only letters to properly transform the names for our output.
            //The regex was found at http://stackoverflow.com/questions/16426976
            $key = trim(preg_replace('~[^\p{L}]++~u', ' ', $values[0]->textContent));
            $key = 'set'.str_replace(' ', '', $key);

            $stats->$key((float) $value);
        }

        return $stats;
    }

    public static function parseFriends($contents)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');
        $maincontent = $crawler->filter('.friendHolder');

        //Empty array so we return something non-null if the list is empty.
        $friendlist = array();

        foreach ($maincontent as $friendentry) {
            $crawler = new Crawler($friendentry);

            //All the data extraction.
            $avatar = $crawler->filter('.friendIcon')->filterXPath('./div/a/img');
            $name = $crawler->filterXPath('//div[@class="friendBlock"]/div[2]/a')->text();
            $lastonline = $crawler->filterXPath('./div/div/div[3]')->text();
            $friendssince = (new Date)->formatTime(str_replace('Friends since ', '', $crawler->filterXPath('./div/div/div[4]')->text()));

            //Remove the tumbnail portions from the URL to get the full image.
            $avatar = str_replace('thumbs/', '', str_replace('_thumb', '', $avatar->attr('src')));

            $friendinfo['name'] = $name;
            $friendinfo['friend_since'] = $friendssince;

            //Fill out a profile object for this user with what information we can extract
            $friendinfo['profile'] = new Profile();
            $friendinfo['profile']->setAvatarUrl($avatar);
            $friendinfo['profile']->details->setLastOnline($lastonline);

            //to force json array and !objects.
            $friendinfo['profile']->manga_stats = null;
            $friendinfo['profile']->anime_stats = null;

            $friendlist[] = $friendinfo;
        }

        return $friendlist;

    }

    public static function parseHistory($contents)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');
        $maincontent = $crawler->filter('table')->filter('tr');

        //Empty array so we return something non-null if the list is empty.
        $historylist = array();

        foreach ($maincontent as $historyentry) {
            $crawler = new Crawler($historyentry);

            // bypass for the MAL generated strings
            if (($crawler->filter('a')->count()) > 0){

                if (strpos($crawler->filter('a')->attr('href'), 'anime') == true) {
                    $historyinfo['item'] = new Anime();
                    $historyinfo['item']->setEpisodes((int) $crawler->filter('strong')->text());
                    $historyinfo['type'] = 'anime';
                } else {
                    $historyinfo['item'] = new Manga();
                    $historyinfo['item']->setChapters((int) $crawler->filter('strong')->text());
                    $historyinfo['type'] = 'manga';
                }

                $historyinfo['item']->setTitle($crawler->filter('a')->text());
                $historyinfo['item']->setId((int) str_replace('/anime.php?id=', '', $crawler->filter('a')->attr('href')));
                $historyinfo['time_updated'] = (new Date)->formatTime(substr($crawler->filter('td')->eq(1)->text(), 1));

                $historylist[] = $historyinfo;
            }
        }

        return $historylist;

    }
}
