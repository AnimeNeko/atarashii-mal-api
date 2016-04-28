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

class SearchParser
{
    public static function parse($contents, $type)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $result = array();

        $items = $crawler->filter('entry');
        if ($type === 'anime') {
            foreach ($items as $item) {
                $result[] = self::parseAnime($item);
            }
        } else {
            foreach ($items as $item) {
                $result[] = self::parseManga($item);
            }
        }

        return $result;
    }

    private static function parseAnime($item)
    {
        $crawler = new Crawler($item);
        $anime = new Anime();
        $anime->setId($crawler->filter('id')->text());
        $anime->setTitle($crawler->filter('title')->text());

        $otherTitles = array();
        $english = explode('; ', $crawler->filter('english')->text());
        if (count($english) > 0 && $english !== '') {
            $otherTitles['english'] = $english;
        }

        $synonyms = explode('; ', $crawler->filter('synonyms')->text());
        if (count($synonyms) > 0 && $synonyms[0] !== '') {
            $otherTitles['synonyms'] = $synonyms;
        }
        $anime->setOtherTitles($otherTitles);

        $anime->setEpisodes($crawler->filter('episodes')->text());
        $anime->setMembersScore($crawler->filter('score')->text());
        $anime->setStatus($crawler->filter('status')->text());

        $startDate = $crawler->filter('start_date')->text();
        if ($startDate !== '0000-00-00') {
            $anime->setStartDate((new \DateTime())->createFromFormat('Y-m-d', $startDate));
        }

        $EndDate = $crawler->filter('end_date')->text();
        if ($EndDate !== '0000-00-00') {
            $anime->setEndDate((new \DateTime())->createFromFormat('Y-m-d', $EndDate));
        }

        $anime->setSynopsis($crawler->filter('synopsis')->text());
        $anime->setImageUrl($crawler->filter('image')->text());

        return $anime;
    }

    private static function parseManga($item)
    {
        $crawler = new Crawler($item);
        $manga = new Manga();
        $manga->setId($crawler->filter('id')->text());
        $manga->setTitle($crawler->filter('title')->text());

        $otherTitles = array();
        $english = explode('; ', $crawler->filter('english')->text());
        if (count($english) > 0 && $english !== '') {
            $otherTitles['english'] = $english;
        }

        $synonyms = explode('; ', $crawler->filter('synonyms')->text());
        if (count($synonyms) > 0 && $synonyms[0] !== '') {
            $otherTitles['synonyms'] = $synonyms;
        }
        $manga->setOtherTitles($otherTitles);

        $manga->setOtherTitles($otherTitles);
        $manga->setChapters($crawler->filter('chapters')->text());
        $manga->setVolumes($crawler->filter('volumes')->text());
        $manga->setMembersScore($crawler->filter('score')->text());
        $manga->setStatus($crawler->filter('status')->text());

        $startDate = $crawler->filter('start_date')->text();
        if ($startDate !== '0000-00-00') {
            $manga->setStartDate((new \DateTime())->createFromFormat('Y-m-d', $startDate));
        }

        $EndDate = $crawler->filter('end_date')->text();
        if ($EndDate !== '0000-00-00') {
            $manga->setEndDate((new \DateTime())->createFromFormat('Y-m-d', $EndDate));
        }

        $manga->setSynopsis($crawler->filter('synopsis')->text());
        $manga->setImageUrl($crawler->filter('image')->text());

        return $manga;
    }
}
