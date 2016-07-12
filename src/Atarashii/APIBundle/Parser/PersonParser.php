<?php
/**
 * Atarashii MAL API.
 *
 * @author    Kyle Lanchman <k.lanchman@gmail.com>
 * @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
 * @author    Michael Johnson <youngmug@animeneko.net>
 * @copyright 2014-2016 Ratan Dhawtal and Michael Johnson
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
 */

namespace Atarashii\APIBundle\Parser;

use Atarashii\APIBundle\Model\Anime;
use Atarashii\APIBundle\Model\Manga;
use Symfony\Component\DomCrawler\Crawler;
use Atarashii\APIBundle\Model\Person;
use DateTime;

class PersonParser
{
    public static function parse($contents)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $personrecord = new Person();

        # Person ID.
        # Example:
        # <input type="hidden" name="vaid" value="185">
        $personrecord->setId((int) $crawler->filter('input[name="vaid"]')->attr('value'));

        # Name
        # Example:
        # <div><h1 class="h1">Hanazawa, Kana</h1></div>
        $personrecord->setName(trim($crawler->filter('div h1')->text()));

        # Image
        # Example:
        # <a href="/people/185/Kana_Hanazawa/pictures"><img src="http://cdn.myanimelist.net/images/voiceactors/3/43500.jpg" alt="Hanazawa, Kana"></a>
        $personrecord->setImageUrl($crawler->filter('div#content tr td div img')->attr('src'));

        $leftcolumn = $crawler->filterXPath('//div[@id="content"]/table/tr/td[@class="borderClass"]');

        # Given name
        $extracted = $leftcolumn->filterXPath('//span[text()="Given name:"]');
        if ($extracted->count() > 0) {
            $personrecord->setGivenName(trim(str_replace($extracted->text(), '', $extracted->parents()->text())));
        }

        # Family name
        # MAL messed this field up. It's not wrapped in a div, so the text is floating out in the td.
        $extracted = $leftcolumn->filterXPath('//span[text()="Family name:"]');
        if ($extracted->count() > 0) {
            $matches = array();

            # This regex matches "Family name:..." until it hits Birthday/Website/Alternate [name], one of which should
            # be the field following the Family name field
            preg_match('/Family name:.*?(?:(?!Birthday|Website|Alternate).)*/', $leftcolumn->text(), $matches);

            if (count($matches) > 0) {
                $personrecord->setFamilyName(trim(str_replace($extracted->text(), '', $matches[0])));
            }
        }

        # Alternate names
        $extracted = $leftcolumn->filterXPath('//span[text()="Alternate names:"]');
        if ($extracted->count() > 0) {
            $text = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $alternateNames = explode(', ', $text);
            $personrecord->setAlternateNames($alternateNames);
        }

        # Birthday
        $extracted = $leftcolumn->filterXPath('//span[text()="Birthday:"]');
        if ($extracted->count() > 0) {
            $dateStr = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $dateStr = str_replace('  ', ' ', $dateStr); // Replace 2 spaces with 1, MAL seems to add 2 spaces in some dates
            if (strpos($dateStr, ',') === false) {
                if (strlen($dateStr) === 4) { // Only a year, Example ID 11746
                    $personrecord->setBirthday(DateTime::createFromFormat('Y m d', $dateStr.' 01 01'), 'year');
                } elseif (count(explode(' ', $dateStr)) == 2) { // Month + Year, i.e. "Feb 1999", Example ID 7277
                    $dateComponents = explode(' ', $dateStr);
                    $month = $dateComponents[0];
                    $year = $dateComponents[1];
                    $personrecord->setBirthday(DateTime::createFromFormat('M Y d', $month.' '.$year.' 01'), 'month');
                }
            } else {
                $dateComponents = explode(' ', $dateStr);
                if (count($dateComponents) == 2) { // Month + Day, i.e. "Jun 15,", Example ID 2608
                    $month = $dateComponents[0];
                    $day = substr($dateComponents[1], 0, -1);
                    $personrecord->setBirthday(DateTime::createFromFormat('M d Y', $month.' '.$day.' 1970'), 'dayMonth');
                } elseif (count($dateComponents) == 3) { // Full date, i.e. "Feb 25, 1989", Example ID 185
                    $personrecord->setBirthday(DateTime::createFromFormat('M j, Y', $dateStr), 'day');
                }
            }
        }

        # Website
        # This isn't in a div, but the anchor element is the next sibling of the span
        $extracted = $leftcolumn->filterXPath('//span[text()="Website:"]');
        if ($extracted->count() > 0) {
            $personrecord->setWebsiteUrl(trim(str_replace($extracted->text(), '', $extracted->nextAll()->attr("href"))));
        }

        # Favorites count
        $extracted = $leftcolumn->filterXPath('//span[text()="Member Favorites:"]');
        if ($extracted->count() > 0) {
            $personrecord->setFavoritedCount(trim(str_replace($extracted->text(), '', $extracted->parents()->text())));
        }

        # More Details
        # Note: CSS classes are misspelled, need to keep an eye on this
        $extracted = $leftcolumn->filter('div[class="people-informantion-more js-people-informantion-more"]');
        if ($extracted->count() > 0) {
            $personrecord->setMoreDetails($extracted->html());
        }

        # Extract from sections on the right column: Voice acting roles, anime staff positions, published manga
        $rightcolumn = $crawler->filter('div[id="content"] td[style="padding-left: 5px;"]');

        // Voice acting roles
        $vaRoles = $rightcolumn->filterXPath('//div[text()="Voice Acting Roles"]');
        if ($vaRoles->count() > 0) {
            $rolesArray = array();

            // Iterate over each row in the table
            foreach ($vaRoles->nextAll()->children() as $item) {
                $node = new Crawler($item);
                // Fill in the character details
                $characterAnchor = $node->filterXPath('//td[3]/a');
                $characterName = $characterAnchor->text();
                $characterUrl = $characterAnchor->attr('href');
                $isMainCharacer = strpos($node->filterXPath('//td[3]/div')->text(), "Main") !== false;
                $characterImage = preg_replace('/r(.+?)\/(.+?)\?(.+?)$/', '$2', $node->filterXPath('//td[4]/div/a/img')->attr('data-src'));

                $match = preg_match('/\/(character)\/(\d+)\/.*?/', $characterUrl, $urlParts);
                if ($match !== false && $match !== 0) {
                    $characterId = (int) $urlParts[2];
                }

                $itemArray = array(
                    'id' => $characterId,
                    'name' => $characterName,
                    'image_url' => $characterImage,
                    'main_role' => $isMainCharacer,
                );

                $itemArray['anime'] = new Anime();
                $animeDetails = $node->filterXPath('//td[2]/a');

                // Fill in the anime details
                $itemArray['anime']->setImageUrl(preg_replace('/r(.+?)\/(.+?)\?(.+?)$/', '$2', $node->filterXPath('//td[1]/div/a/img')->attr('data-src')));
                $itemArray['anime']->setTitle($animeDetails->text());

                $match = preg_match('/\/(anime)\/(\d+)\/.*?/', $animeDetails->attr('href'), $urlParts);
                if ($match !== false && $match !== 0) {
                    $itemArray['anime']->setId($urlParts[2]);
                }

                $rolesArray[] = $itemArray;
            }
            $personrecord->setVoiceActingRoles($rolesArray);
        }

        // Anime staff positions
        $staffPositions = $rightcolumn->filterXPath('//div[text()="Anime Staff Positions"]');
        if ($staffPositions->count() > 0) {
            $positionsArray = array();

            // Iterate over each row in the table
            foreach ($staffPositions->nextAll()->children() as $item) {
                $node = new Crawler($item);

                // Fill in the position details
                $position = $node->filterXPath('//td[2]/div/small')->text();
                $positionDetails = $node->filterXPath('//td[2]/div')->text();

                // Details are wrapped in parenthesis, so we'll find those parens and grab what's inside
                // Sometimes what comes back is malformed - i.e. "ep. 1-15)," - it appears malformed on the site too
                $match = preg_match('/\((.*)\)/', $positionDetails, $positionDetailsParts);
                if ($match !== false && $match !== 0) {
                    $positionDetails = $positionDetailsParts[1];
                } else {
                    $positionDetails = null;
                }

                $itemArray = array(
                    'position' => $position,
                    'details' => $positionDetails,
                );

                $itemArray['anime'] = new Anime();
                $animeDetails = $node->filterXPath('//td[2]/a');

                // Fill in the anime details
                $itemArray['anime']->setImageUrl(preg_replace('/r(.+?)\/(.+?)\?(.+?)$/', '$2', $node->filterXPath('//td[1]/div/a/img')->attr('data-src')));
                $itemArray['anime']->setTitle($animeDetails->text());

                $match = preg_match('/\/(anime)\/(\d+)\/.*?/', $animeDetails->attr('href'), $urlParts);
                if ($match !== false && $match !== 0) {
                    $itemArray['anime']->setId($urlParts[2]);
                }
                $positionsArray[] = $itemArray;

            }
            $personrecord->setAnimeStaffPositions($positionsArray);
        }

        // Published manga
        $mangaPositions = $rightcolumn->filterXPath('//div[text()="Published Manga"]');
        if ($mangaPositions->count() > 0) {
            $positionsArray = array();

            // Iterate over each row in the table
            foreach ($mangaPositions->nextAll()->children() as $item) {
                $node = new Crawler($item);
                // Fill in the position
                $position = $node->filterXPath('//td[2]/div/small')->text();
                $itemArray = array(
                    'position' => $position,
                );

                $itemArray['manga'] = new Manga();
                $mangaDetails = $node->filterXPath('//td[2]/a');

                // Fill in the manga details
                $itemArray['manga']->setImageUrl(preg_replace('/r(.+?)\/(.+?)\?(.+?)$/', '$2', $node->filterXPath('//td[1]/div/a/img')->attr('data-src')));
                $itemArray['manga']->setTitle($mangaDetails->text());

                $match = preg_match('/\/(manga)\/(\d+)\/.*?/', $mangaDetails->attr('href'), $urlParts);
                if ($match !== false && $match !== 0) {
                    $itemArray['manga']->setId($urlParts[2]);
                }

                $positionsArray[] = $itemArray;
            }
            $personrecord->setPublishedManga($positionsArray);
        }

        return $personrecord;
    }
}