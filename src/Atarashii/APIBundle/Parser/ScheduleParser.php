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

use Atarashii\APIBundle\Model\Anime;
use Symfony\Component\DomCrawler\Crawler;

class ScheduleParser
{
    public static function parse($contents)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $classDay = 'seasonal-anime-list js-seasonal-anime-list js-seasonal-anime-list-key-';
        $recordContainer = 'div[class="seasonal-anime js-seasonal-anime"]';

        $result = array();
        $result['monday'] = self::parseDay($crawler->filter('div[class="'.$classDay.'monday clearfix"] '.$recordContainer));
        $result['tuesday'] = self::parseDay($crawler->filter('div[class="'.$classDay.'tuesday clearfix"] '.$recordContainer));
        $result['wednesday'] = self::parseDay($crawler->filter('div[class="'.$classDay.'wednesday clearfix"] '.$recordContainer));
        $result['thursday'] = self::parseDay($crawler->filter('div[class="'.$classDay.'thursday clearfix"] '.$recordContainer));
        $result['friday'] = self::parseDay($crawler->filter('div[class="'.$classDay.'friday clearfix"] '.$recordContainer));
        $result['saturday'] = self::parseDay($crawler->filter('div[class="'.$classDay.'saturday clearfix"] '.$recordContainer));
        $result['sunday'] = self::parseDay($crawler->filter('div[class="'.$classDay.'sunday clearfix"] '.$recordContainer));
        $result['other'] = self::parseDay($crawler->filter('div[class="'.$classDay.'other clearfix"] '.$recordContainer));
        $result['unknown'] = self::parseDay($crawler->filter('div[class="'.$classDay.'unknown clearfix"] '.$recordContainer));

        return $result;
    }

    public static function parseDay($rows)
    {
        $result = array();
        foreach ($rows as $item) {
            $crawler = new Crawler($item);
            $anime = new Anime();

            $url = $crawler->filter('a[class="link-title"]')->attr('href');
            $id = preg_match('/\/(anime|manga)\/(\d+)\/.*?/', $url, $urlParts);

            if ($id !== false || $id !== 0) {
                $anime->setId((int) $urlParts[2]);
            }
            $anime->setTitle(trim($crawler->filter('a[class="link-title"]')->text()));
            $producer = $crawler->filter('span[class="producer"] a');
            if ($producer->count() > 0) {
                $anime->setProducers(explode(', ', $crawler->filter('span[class="producer"] a')->text()));
            }
            $anime->setEpisodes((int) str_replace(' eps', '', $crawler->filter('div[class="eps"] span')->text()));

            $genres = $crawler->filterXPath('//div[contains(@class, "genres")]//span[@class="genre"]');

            $genreArray = array();

            foreach ($genres as $genre) {
                $genreArray[] = trim($genre->nodeValue);
            }

            $anime->setGenres($genreArray);

            //Image
            $image = $crawler->filterXPath('//div[@class="image"]/img');
            if ($image->count() > 0) {
                if (empty($image->attr('src'))) {
                    $imageUrl = $image->attr('data-src');
                } else {
                    $imageUrl = $image->attr('src');
                }

                if (preg_match('/(.*?)\?/', $imageUrl, $imageUrlMatches) === 1) { //Remove Query String parameters
                    $anime->setImageUrl(preg_replace('/\/r\/\d+x\d+/', '', $imageUrlMatches[1])); //Remove size parameter (to get largest size)
                }
            }

            $anime->setSynopsis(trim($crawler->filter('div[class="synopsis js-synopsis"]')->text()));
            $detail = explode('-', $crawler->filter('div[class="info"]')->text());
            $anime->setType(trim($detail[0]));
            $anime->setMembersCount((int) str_replace(',', '', trim($crawler->filter('span[class="member fl-r"]')->text())));
            $anime->setMembersScore((float) trim($crawler->filter('span[class="score"]')->text()));

            $result[] = $anime;
        }

        return $result;
    }
}
