<?php
/**
 * Atarashii MAL API.
 *
 * @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
 * @author    Michael Johnson <youngmug@animeneko.net>
 * @copyright 2014-2017 Ratan Dhawtal and Michael Johnson
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
 */

namespace Atarashii\APIBundle\Parser;

use Atarashii\APIBundle\Model\Anime;
use Atarashii\APIBundle\Model\Manga;
use SimpleXMLElement;

class ListParser
{
    /**
     * Parse the personal anime or manga list XML.
     *
     * @param string $contents The HTML page source
     * @param string $type     A string detailing the record type (anime or manga)
     *
     * @return array An array containing statistics and an array of Anime or Manga items
     */
    public static function parse($contents, $type)
    {
        $listXml = new SimpleXMLElement($contents);
        $itemList = array();

        $i = 0;
        foreach ($listXml->$type as $item) {
            if ($type === 'anime') {
                $itemList[$i] = new Anime();
            } else {
                $itemList[$i] = new Manga();
            }

            // Items named the same for anime and manga
            $startDate = self::parseDate($item->series_start);
            $endDate = self::parseDate($item->series_end);
            $personalStartDate = self::parseDate($item->my_start_date);
            $personalEndDate = self::parseDate($item->my_finish_date);

            $itemList[$i]->setTitle((string) $item->series_title);
            $itemList[$i]->setType((int) $item->series_type);
            $itemList[$i]->setStatus((int) $item->series_status);
            $itemList[$i]->setImageUrl((string) $item->series_image);
            $itemList[$i]->setScore((int) $item->my_score);
            $itemList[$i]->setLastUpdated((int) $item->my_last_updated);

            if (null !== $startDate) {
                $itemList[$i]->setStartDate($startDate['date'], $startDate['accuracy']);
            }

            if (null !== $endDate) {
                $itemList[$i]->setEndDate($endDate['date'], $endDate['accuracy']);
            }

            // The personal tags are passed by MAL as string.
            // This will convert it into an array.
            $myTags = (string) $item->my_tags;
            if (strlen($myTags) > 0) {
                $tagArray = array();
                $personalTags = explode(',', trim($myTags));

                foreach ($personalTags as $tag) {
                    $tagArray[] = trim($tag);
                }

                $itemList[$i]->setPersonalTags($tagArray);
            }

            // The synonyms are passed by MAL as string.
            // This will convert it into an array.
            $titleSynonyms = (string) $item->series_synonyms;
            if (strlen($titleSynonyms) > 0) {
                $titleArray = array();
                $rawSynonyms = explode(';', trim($titleSynonyms));

                foreach ($rawSynonyms as $synonym) {
                    if (trim($synonym) !== '') {
                        $titleArray[] = trim($synonym);
                    }
                }

                $otherTitles['synonyms'] = $titleArray;

                $itemList[$i]->setOtherTitles($otherTitles);
            }

            if ($type === 'anime') {
                $itemList[$i]->setId((int) $item->series_animedb_id);
                $itemList[$i]->setListedAnimeId((int) $item->my_id);

                $itemList[$i]->setEpisodes((int) $item->series_episodes);
                $itemList[$i]->setWatchedEpisodes((int) $item->my_watched_episodes);

                if (null !== $personalStartDate) {
                    $itemList[$i]->setWatchingStart($personalStartDate['date']);
                }
                if (null !== $personalEndDate) {
                    $itemList[$i]->setWatchingEnd($personalEndDate['date']);
                }

                $itemList[$i]->setWatchedStatus((int) $item->my_status);

                $itemList[$i]->setRewatching(((int) $item->my_rewatching) === 1);
            } else {
                $itemList[$i]->setId((int) $item->series_mangadb_id);
                $itemList[$i]->setListedMangaId((int) $item->my_id);

                $itemList[$i]->setChapters((int) $item->series_chapters);
                $itemList[$i]->setVolumes((int) $item->series_volumes);

                $itemList[$i]->setChaptersRead((int) $item->my_read_chapters);
                $itemList[$i]->setVolumesRead((int) $item->my_read_volumes);

                if (null !== $personalStartDate) {
                    $itemList[$i]->setReadingStart($personalStartDate['date']);
                }

                if (null !== $personalEndDate) {
                    $itemList[$i]->setReadingEnd($personalEndDate['date']);
                }

                $itemList[$i]->setReadStatus((int) $item->my_status);

                $itemList[$i]->setRereading(((int) $item->my_rereadingg) === 1); //Yes, it's my_readreadingg in the XML.
            }

            ++$i;
        }

        $animelist['statistics']['days'] = (float) $listXml->myinfo->user_days_spent_watching;
        $animelist[$type] = $itemList;

        return $animelist;
    }

    /**
     * Parse the date MAL gives in the list XML.
     *
     * This function is needed because MAL gives all 0s in slots where the user did not enter a value.
     *
     * @param string $date The Date from the MAL XML, as a string
     *
     * @return array an Array containing a DateTime and a string indicating the accuracy value (day/month/year)
     */
    private static function parseDate($date)
    {
        //Default Values in the case month and day are not already set.
        $month = 6;
        $day = 15;

        $dateParts = explode('-', $date);

        //Check for a valid year
        if ((int) $dateParts[0] === 0) {
            //If year is empty/0, then just return null
            return null;
        } else {
            $year = (int) $dateParts[0];
            $accuracy = 'year';

            //Check for a valid month
            if ((int) $dateParts[1] !== 0) {
                $accuracy = 'month';
                $month = (int) $dateParts[1];

                //Check for a valid day
                if ((int) $dateParts[2] !== 0) {
                    $accuracy = 'day';
                    $day = (int) $dateParts[2];
                }
            }
        }

        $date = \DateTime::createFromFormat('Y-m-d', "$year-$month-$day");

        return ['date' => $date, 'accuracy' => $accuracy];
    }
}
