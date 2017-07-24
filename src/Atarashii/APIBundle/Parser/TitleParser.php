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
use Atarashii\APIBundle\Model\Manga;
use Atarashii\APIBundle\Model\Person;
use Symfony\Component\DomCrawler\Crawler;

class TitleParser
{
    public static function parse($contents, $apiVersion, $type)
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($contents);

        if ($type === 'anime') {
            $record = new Anime();
        } else {
            $record = new Manga();
        }

        /*
         * Title ID
         */
        if ($type === 'anime') {
            $itemId = $crawler->filterXPath('//input[@name="aid"]');
        } else {
            $itemId = $crawler->filterXPath('//input[@name="mid"]');
        }

        if ($itemId->count() > 0) {
            $record->setId((int) $itemId->attr('value'));
        }

        /*
         * Top Main Title
         */
        $itemTitle = $crawler->filterXPath('//h1/span[@itemprop="name"]');
        $record->setTitle($itemTitle->text());

        //Parse the Sidebar
        $sidebarContent = $crawler->filterXPath('//div[@id="content"]/table/tr/td[1]');
        $record = self::parseSidebar($sidebarContent, $record, $type);

        //Parse the Statistics Box
        $statsContent = $crawler->filterXPath('//div[contains(@class, "anime-detail-header-stats")]');
        $record = self::parseStats($statsContent, $record, $type);

        //Parse the Main Content
        $mainContent = $crawler->filterXPath('//div[@id="content"]/table/tr/td[2]');
        $record = self::parseMain($mainContent, $record, $type);

        return $record;
    }

    /**
     * Parse the personal details page content.
     *
     * @param string $contents The HTML page source
     * @param object $record   An Anime or Manga model
     * @param string $type     A string detailing the record type (anime or manga)
     */
    public static function parseExtendedPersonal($contents, $record, $type)
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($contents);

        $crawler = $crawler->filterXPath('//form[@id="main-form"]');
        if ($crawler->count() > 0) {
            //Rewatching
            $extracted = $crawler->filterXPath('//input[@id="add_anime_is_rewatching"]');
            if ($extracted->count() > 0) {
                $rewatching = $extracted->attr('checked');

                if ($rewatching === null) {
                    $record->setRewatching(false);
                } else {
                    $record->setRewatching(true);
                }
            }

            //Start and End Dates
            $isStarted = $crawler->filterXPath('//input[@id="unknown_start"]')->attr('checked');
            $isEnded = $crawler->filterXPath('//input[@id="unknown_end"]')->attr('checked');
            $startDate = null;
            $endDate = null;

            if ($isStarted != 'checked') {
                $extracted = $crawler->filterXPath('//input[@id="unknown_start"]/../../..');
                if ($extracted->count() > 0) {
                    $startDate = self::parsePersonalDate($extracted, $type, 'start');
                }
            }

            if ($isEnded != 'checked') {
                $extracted = $crawler->filterXPath('//input[@id="unknown_end"]/../../..');
                if ($extracted->count() > 0) {
                    $endDate = self::parsePersonalDate($extracted, $type, 'finish');
                }
            }

            //Tags
            $extracted = $crawler->filterXPath('//textarea[@id="add_'.$type.'_tags"]');
            if ($extracted->count() > 0 && strlen($extracted->text()) > 0) {
                $personalTags = explode(',', $extracted->text());

                foreach ($personalTags as $tag) {
                    $tagArray[] = trim($tag);
                }

                $record->setPersonalTags($tagArray);
            }

            //Priority
            $extracted = $crawler->filterXPath('//select[@id="add_'.$type.'_priority"]/option[@selected]');
            if ($extracted->count() > 0) {
                $record->setPriority((int) $extracted->attr('value'));
            }

            //Rewatch/Reread Count
            $reConsumedCount = null;
            if ($type === 'anime') {
                $extracted = $crawler->filterXPath('//input[@id="add_'.$type.'_num_watched_times"]');
            } else {
                $extracted = $crawler->filterXPath('//input[@id="add_'.$type.'_num_read_times"]');
            }
            if ($extracted->count() > 0) {
                $extracted = (int) $extracted->attr('value');
                if ($extracted > 0) {
                    $reConsumedCount = $extracted;
                }
            }

            //Rewatch/Reread Value
            $reConsumeValue = null;
            if ($type === 'anime') {
                $extracted = $crawler->filterXPath('//select[@id="add_'.$type.'_rewatch_value"]/option[@selected]');
            } else {
                $extracted = $crawler->filterXPath('//select[@id="add_'.$type.'_reread_value"]/option[@selected]');
            }
            if ($extracted->count() > 0) {
                $extracted = (int) $extracted->attr('value');
                if ($extracted > 0) {
                    $reConsumeValue = $extracted;
                }
            }

            //Comments
            $extracted = $crawler->filterXPath('//textarea[@id="add_'.$type.'_comments"]');
            if ($extracted->count() > 0 && strlen($extracted->text()) > 0) {
                $record->setPersonalComments(trim($extracted->text()));
            }

            //Type specific items
            if ($type === 'anime') {
                $record->setWatchingStart($startDate);
                $record->setWatchingEnd($endDate);

                if ($reConsumedCount > 0) {
                    $record->setRewatchCount($reConsumedCount);
                }

                if ($reConsumeValue > 0) {
                    $record->setRewatchValue($reConsumeValue);
                }

                //Storage Type
                $extracted = $crawler->filterXPath('//select[@id="add_'.$type.'_storage_type"]/option[@selected]');
                if ($extracted->count() > 0) {
                    $extracted = (int) $extracted->attr('value');
                    if ($extracted > 0) {
                        $record->setStorage($extracted);
                    }
                }

                //Storage Value
                $extracted = $crawler->filterXPath('//input[@id="add_'.$type.'_storage_value"]');
                if ($extracted->count() > 0) {
                    $extracted = (float) $extracted->attr('value');
                    if ($extracted > 0) {
                        $record->setStorageValue($extracted);
                    }
                }
            } else {
                $record->setReadingStart($startDate);
                $record->setReadingEnd($endDate);

                if ($reConsumedCount > 0) {
                    $record->setRereadCount($reConsumedCount);
                }

                if ($reConsumeValue > 0) {
                    $record->setRereadValue($reConsumeValue);
                }
            }
        }

//        var_dump($record); die();
        return $record;
    }

    private static function parseSidebar(Crawler $sidebarContent, $record, $type)
    {
        /*
         * Title Image
         */
        $extracted = $sidebarContent->filterXPath('//img[@itemprop="image"]');
        if (count($extracted) > 0) {
            $extracted = $extracted->attr('src');
            $extracted = str_replace('t.jpg', '.jpg', $extracted); //Sometimes we get a thumbnail. Remove the thumbnail suffix.
            $record->setImageUrl($extracted);
        }

        /*
         * Alternative Titles
         * These aren't in their own contained section, so we need to pull each item individually.
         */

        //English
        $extracted = $sidebarContent->filterXPath('//span[text()="English:"]');
        if ($extracted->count() > 0) {
            $extracted = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $otherTitles['english'] = explode(', ', $extracted);
            $record->setOtherTitles($otherTitles);
        }

        //Synonyms
        $extracted = $sidebarContent->filterXPath('//span[text()="Synonyms:"]');
        if ($extracted->count() > 0) {
            $extracted = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $otherTitles['synonyms'] = explode(', ', $extracted);
            $record->setOtherTitles($otherTitles);
        }

        //Japanese
        $extracted = $sidebarContent->filterXPath('//span[text()="Japanese:"]');
        if ($extracted->count() > 0) {
            $extracted = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $otherTitles['japanese'] = explode(', ', $extracted);
            $record->setOtherTitles($otherTitles);
        }

        /*
         * General Information
         * These aren't in their own contained section, so we need to pull each item individually.
         */

        //Type
        $extracted = $sidebarContent->filterXPath('//span[text()="Type:"]');
        if ($extracted->count() > 0) {
            $extracted = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $record->setType($extracted);
        }

        //Status
        $extracted = $sidebarContent->filterXPath('//span[text()="Status:"]');
        if ($extracted->count() > 0) {
            $extracted = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $record->setStatus(strtolower($extracted));
        }

        //Genres
        $extracted = $sidebarContent->filterXPath('//span[text()="Genres:"]');
        $hasGenre = $extracted->count() > 0 
            && strpos($extracted->parents()->text(), 'None found') === false 
            && strpos($extracted->parents()->text(), 'No genres') === false;

        if ($hasGenre) {
            $records = $extracted->parents()->first()->filterXPath('//a');

            foreach ($records as $genreRecord) {
                $genres[] = $genreRecord->nodeValue;
            }

            $record->setGenres($genres);
        }

        //Aired (Anime) or Published (Manga)
        //Note: MAL shows dates in US format (Month Day, Year)
        if ($type === 'anime') {
            $extracted = $sidebarContent->filterXPath('//span[text()="Aired:"]');
        } else {
            $extracted = $sidebarContent->filterXPath('//span[text()="Published:"]');
        }
        if ($extracted->count() > 0) {
            $extracted = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));

            if ($extracted !== 'Not available') {
                $dateRange = explode(' to ', $extracted);

                //Start Date
                $startDate = self::parseTitleDate($dateRange[0]);

                if ($startDate !== null) {
                    $record->setStartDate($startDate[0], $startDate[1]);
                }

                //End Date
                //Series yet to air won't list a range, and currently airing use a "?" for the end.
                if (count($dateRange) > 1 && $dateRange[1] !== '?') {
                    $endDate = self::parseTitleDate($dateRange[1]);
                    if ($endDate !== null) {
                        $record->setEndDate($endDate[0], $endDate[1]);
                    }
                }
            }
        }

        //Type specific properties
        if ($type === 'anime') {
            //Episodes
            $extracted = $sidebarContent->filterXPath('//span[text()="Episodes:"]');
            if ($extracted->count() > 0) {
                $extracted = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
                $record->setEpisodes((int) $extracted);
            }

            //Broadcast
            if (strpos($record->getStatus(), 'finished') === false) {
                $extracted = $sidebarContent->filterXPath('//span[text()="Broadcast:"]');
                if ($extracted->count() > 0) {
                    $extracted = str_replace($extracted->text(), '', $extracted->parents()->text());
                    $extracted = trim(preg_replace('/(\w.+)s at(\s\d.+)\((\w.+)\)/', '$1$2$3', $extracted));
                    $record->setBroadcast($extracted);
                }
            }

            //Producers
            $extracted = $sidebarContent->filterXPath('//span[text()="Producers:"]');
            if ($extracted->count() > 0 && strpos($extracted->parents()->text(), 'None found') === false) {
                $records = $extracted->parents()->first()->filterXPath('//a');

                foreach ($records as $producerRecord) {
                    $producers[] = $producerRecord->nodeValue;
                }

                $record->setProducers($producers);
            }

            //Licensors
            $extracted = $sidebarContent->filterXPath('//span[text()="Licensors:"]');
            if ($extracted->count() > 0 && strpos($extracted->parents()->text(), 'None found') === false) {
                $records = $extracted->parents()->first()->filterXPath('//a');

                foreach ($records as $licensorRecord) {
                    $licensors[] = $licensorRecord->nodeValue;
                }

                $record->setLicensors($licensors);
            }

            //Studios
            $extracted = $sidebarContent->filterXPath('//span[text()="Studios:"]');
            if ($extracted->count() > 0 && strpos($extracted->parents()->text(), 'None found') === false) {
                $records = $extracted->parents()->first()->filterXPath('//a');

                foreach ($records as $studioRecord) {
                    $studios[] = $studioRecord->nodeValue;
                }

                $record->setStudios($studios);
            }

            //Source
            $extracted = $sidebarContent->filterXPath('//span[text()="Source:"]');
            if ($extracted->count() > 0) {
                $extracted = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
                $record->setSource($extracted);
            }

            //Duration
            $extracted = $sidebarContent->filterXPath('//span[text()="Duration:"]');
            if ($extracted->count() > 0) {
                $duration = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));

                if (strpos($duration, 'min.') !== false) {
                    if (strpos($duration, 'hr.') !== false) { //contains hours and minutes
                        preg_match('/([0-9]+) hr\. ([0-9]+) min\./', $duration, $durationParts);
                        //This could all be done in one line, but it's more understandable and maintainable broken up.
                        $hours = (int) $durationParts[1];
                        $minutes = (int) $durationParts[2];
                        $record->setDuration(($hours * 60) + $minutes);
                    } else { //contains only minutes
                        preg_match('/([0-9]+) min\./', $duration, $durationParts);
                        $record->setDuration((int) $durationParts[1]);
                    }
                    //Handle hour-only durations
                } elseif (strpos($duration, 'hr.') !== false) {
                    preg_match('/([0-9]+) hr\./', $duration, $durationParts);
                    $record->setDuration((int) $durationParts[1] * 60);
                }
                // Any other format (such as just "Unknown") isn't understood and is ignored
            }

            //Classification/Rating
            $extracted = $sidebarContent->filterXPath('//span[text()="Rating:"]');
            if ($extracted->count() > 0) {
                $extracted = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
                $record->setClassification($extracted);
            }
        } else {
            //Volumes
            $extracted = $sidebarContent->filterXPath('//span[text()="Volumes:"]');
            if ($extracted->count() > 0) {
                $extracted = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
                if (strpos($extracted, 'Unknown') === false) {
                    $record->setVolumes((int) $extracted);
                }
            }

            //Chapters
            $extracted = $sidebarContent->filterXPath('//span[text()="Chapters:"]');
            if ($extracted->count() > 0) {
                $extracted = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
                if (strpos($extracted, 'Unknown') === false) {
                    $record->setChapters((int) $extracted);
                }
            }

            //Authors
            $extracted = $sidebarContent->filterXPath('//span[text()="Authors:"]');
            if ($extracted->count() > 0 && strpos($extracted->parents()->text(), 'None found') === false) {
                $extracted = trim(str_replace($extracted->html(), '', $extracted->parents()->html()));

                $persons = explode(', <', $extracted);
                $authors = array();

                foreach ($persons as $person) {
                    // This relies heavily on the MAL HTML format and will break if they change it.
                    if (preg_match('/people\/(\d+)\/.*?>(.*?)<.*?\((.*)\)$/', $person, $personParts)) {
                        // 1 = MAL Person ID, 2 = Name, 3 = Position
                        $person = new Person();
                        $person->setId((int) $personParts[1]);
                        $person->setName($personParts[2]);

                        $authors[$personParts[3]][] = $person;
                    }
                }

                $record->setAuthors($authors);
            }

            //Serialization
            //There only appears to ever be one linked item.
            $extracted = $sidebarContent->filterXPath('//span[text()="Serialization:"]');
            if ($extracted->count() > 0 && strpos($extracted->parents()->text(), 'None') === false) {
                $extracted = $extracted->parents()->first()->filterXPath('//a');
                $record->setSerialization($extracted->text());
            }
        }

        /*
         * Statistics
         * These aren't in their own contained section, so we need to pull each item individually.
         */

        //Score      - USE THE TOP STATS SECTION
        //Ranking    - USE THE TOP STATS SECTION
        //Popularity - USE THE TOP STATS SECTION
        //Members    - USE THE TOP STATS SECTION

        //Favorites
        //This is the only statistic not yet in the new statistics box
        $extracted = $sidebarContent->filterXPath('//span[text()="Favorites:"]');
        if ($extracted->count() > 0) {
            $extracted = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $extracted = str_replace(',', '', $extracted); //Remove the comma separators so we can convert to an int.
            $record->setFavoritedCount((int) $extracted);
        }

        /*
         * External Links
         * These are only visible to authenticated users, and won't be present for anonymous requests
         */
        $extracted = $sidebarContent->filterXPath('//h2[text()="External Links"]');
        if ($extracted->count() > 0) {
            $records = $extracted->nextAll()->filterXPath('//a');

            foreach ($records as $linkRecord) {
                $record->setExternalLinks($linkRecord->nodeValue, $linkRecord->getAttribute('href'));
            }
        }

        /*
         * Personal Statistics
         * Since this only matters when the user has the title on their list, we do a
         * quick test to see if the heading exists. If so, it's been added and we parse.
         */
        $extracted = $sidebarContent->filterXPath('//h2[text()="Edit Status"]');
        if ($extracted->count() > 0) {
            $personalContent = $sidebarContent->filterXPath('//div[@id="addtolist"]/table');
            if ($personalContent->count() > 0) {
                //Personal Status
                //The exact name depends on the item type, so it's not set until the type-specific section below.
                $extracted = $personalContent->filterXPath('//select[@id="myinfo_status"]/option[@selected="selected"]');
                if ($extracted->count() > 0) {
                    $personalStatus = (int) $extracted->attr('value');
                }

                //Personal Score
                $extracted = $personalContent->filterXPath('//select[@id="myinfo_score"]/option[@selected="selected"]');
                if ($extracted->count() > 0) {
                    $record->setScore((int) $extracted->attr('value'));
                }

                //Listed ID (For getting the detailed status info)
                //The exact name depends on the item type, so it's not set until the type-specific section below.
                $extracted = $personalContent->filterXPath('//a[text()="Edit Details"]');
                if (preg_match('/id=(\d+)/', $extracted->attr('href'), $my_data)) {
                    $personalListedId = (int) $my_data[1];
                }

                if ($type === 'anime') {
                    $record->setWatchedStatus($personalStatus);
                    $record->setListedAnimeId($personalListedId);

                    //Watched Episodes
                    $extracted = $personalContent->filterXPath('//input[@id="myinfo_watchedeps"]');
                    $record->setWatchedEpisodes((int) $extracted->attr('value'));
                } else {
                    $record->setReadStatus($personalStatus);
                    $record->setListedMangaId($personalListedId);

                    //Read Chapters
                    $extracted = $personalContent->filterXPath('//input[@id="myinfo_chapters"]');
                    $record->setChaptersRead((int) $extracted->attr('value'));

                    //Read Volumes
                    $extracted = $personalContent->filterXPath('//input[@id="myinfo_volumes"]');
                    $record->setVolumesRead((int) $extracted->attr('value'));
                }
            }
        }

        return $record;
    }

    private static function parseStats(Crawler $statsContent, $record, $type)
    {
        //Score
        $extracted = $statsContent->filterXPath('//div[@data-title="score"]');
        if ($extracted->count() > 0 && strpos($extracted->text(), 'N/A') === false) {
            $record->setMembersScore((float) trim($extracted->text()));
        }

        //Ranking
        $extracted = $statsContent->filterXPath('//span[contains(@class, "ranked")]/strong');
        if ($extracted->count() > 0 && strpos($extracted->text(), 'N/A') === false) {
            $extracted = str_replace('#', '', $extracted->text());
            $record->setRank((int) $extracted);
        }

        //Popularity
        $extracted = $statsContent->filterXPath('//span[contains(@class, "popularity")]/strong');
        if ($extracted->count() > 0 && strpos($extracted->text(), 'N/A') === false) {
            $extracted = str_replace('#', '', $extracted->text());
            $record->setPopularityRank((int) $extracted);
        }

        //Members
        $extracted = $statsContent->filterXPath('//span[contains(@class, "members")]/strong');
        if ($extracted->count() > 0 && strpos($extracted->text(), 'N/A') === false) {
            $extracted = str_replace(',', '', $extracted->text());
            $record->setMembersCount((int) $extracted);
        }

        return $record;
    }

    private static function parseMain(Crawler $mainContent, $record, $type)
    {
        //Synopsis
        //Note: We set a default text for all titles in the case they have no synopsis
        $record->setSynopsis('There is currently no synopsis for this title.');

        $extracted = $mainContent->filterXPath('//span[@itemprop="description"]');
        if ($extracted->count() > 0) {
            $record->setSynopsis($extracted->html());
        }

        //Background
        $extracted = $mainContent->filterXPath('//h2[text()="Background"]');
        if ($extracted->count() > 0) {
            preg_match('/div>Background<\/h2>(.+?)<div/s', $extracted->parents()->html(), $matches);
            if (strpos($matches[0], 'No background information') === false) {
                $record->setBackground(trim($matches[1]));
            }
        }

        //Video Preview / Promotional Video
        $extracted = $mainContent->filterXPath('//div[@class="video-promotion"]/a');
        if ($extracted->count() > 0) {
            $previewUrl = $extracted->attr('href');
            if (preg_match('/(.*?)\?/', $previewUrl, $previewUrlMatches) === 1) { //Remove Query String parameters
                $record->setPreview($previewUrlMatches[1]);
            }
        }

        //Related Titles
        $extracted = $mainContent->filterXPath('//table[@class="anime_detail_related_anime"]');
        if ($extracted->count() > 0) {
            $extracted = $extracted->children();

            foreach ($extracted as $row) {
                //NOTE: $row is a PHP DOMElement, not a Symfony DOM Crawler
                //This is because iterating over the results is essentially calling "getNode" for each item
                $rowItem = $row->firstChild;

                $relationType = strtr(strtolower(rtrim($rowItem->nodeValue, ':')), ' ', '_');

                //This gets the next td containing the items
                $relatedItem = $rowItem->nextSibling->firstChild;

                do {
                    if ($relatedItem->nodeType !== XML_TEXT_NODE && $relatedItem->tagName == 'a') {
                        $url = $relatedItem->attributes->getNamedItem('href')->nodeValue;
                        $id = preg_match('/\/(anime|manga)\/(\d+)\/.*?/', $url, $urlParts);

                        if (count($urlParts) > 2) {
                            if ($id !== false || $id !== 0) {
                                $itemId = (int) $urlParts[2];
                                $itemTitle = $relatedItem->textContent;
                                $itemUrl = $url;
                            }

                            $itemArray = array();

                            if ($urlParts[1] == 'anime') {
                                $itemArray['anime_id'] = $itemId;
                            } else {
                                $itemArray['manga_id'] = $itemId;
                            }

                            $itemArray['title'] = $itemTitle;
                            $itemArray['url'] = 'https://myanimelist.net'.$itemUrl;

                            $record->addRelation($itemArray, $relationType);
                        }
                    }

                    //Grab next item
                    $relatedItem = $relatedItem->nextSibling;
                } while ($relatedItem !== null);
            }
        }

        //Opening Theme(s)
        //Note: MAL mis-spelled the class as "opnening"
        $extracted = $mainContent->filterXPath('//div[contains(@class, "theme-songs") and contains(@class, "opnening")]//span[@class="theme-song"]');
        if ($extracted->count() > 0) {
            foreach ($extracted as $row) {
                //NOTE: $row is a PHP DOMElement, not a Symfony DOM Crawler
                $record->setOpeningTheme($row->nodeValue);
            }
        }

        //Ending Theme(s)
        $extracted = $mainContent->filterXPath('//div[contains(@class, "theme-songs") and contains(@class, "ending")]//span[@class="theme-song"]');
        if ($extracted->count() > 0) {
            foreach ($extracted as $row) {
                //NOTE: $row is a PHP DOMElement, not a Symfony DOM Crawler
                $record->setEndingTheme($row->nodeValue);
            }
        }

        //Recommendations
        $extracted = $mainContent->filterXPath('//div[@id="anime_recommendation"]//li[@class="btn-anime"]');
        if ($extracted->count() > 0) {
            foreach ($extracted as $row) {
                $recommendation = new Crawler($row);

                //The ID of the recommendation may be on either side of the dash (recs are bi-directional)
                //We'll need to grab the IDs out of the URL and use the one that is not our current title's ID.
                $recUrl = $recommendation->filterXPath('//a')->attr('href');
                if (preg_match('/(\d+)-(\d+)/', $recUrl, $recIds)) {
                    if ($type === 'anime') {
                        $rec = new Anime();
                    } else {
                        $rec = new Manga();
                    }

                    //Remove the first element off the array (which contains both IDs)
                    array_shift($recIds);

                    foreach ($recIds as $recId) {
                        if ($record->getId() != $recId) {
                            $rec->setId((int) $recId);
                        }
                    }

                    $title = $recommendation->filterXPath('//span[contains(@class, "title")]')->text();
                    $rec->setTitle($title);

                    $imageUrl = $recommendation->filterXPath('//img');

                    if ($imageUrl->count() > 0) {
                        $imageUrl = $imageUrl->attr('data-src');

                        if (preg_match('/(.*?)\?/', $imageUrl, $imageUrlMatches) === 1) { //Remove Query String parameters
                            $rec->setImageUrl(preg_replace('/\/r\/\d+x\d+/', '', $imageUrlMatches[1])); //Remove size parameter (to get largest size)
                        }
                    }

                    $record->setRecommendations($rec);
                }
            }
        }

        return $record;
    }

    private static function parseTitleDate($date)
    {
        if (strpos($date, ',') === false) { //Date doesn't contain a comma
            if (strlen($date) === 4) {
                return array(\DateTime::createFromFormat('Y m d', $date.' 01 01'), 'year'); //Year only - "1963" (ex id 6535)
            } elseif ($date !== 'Not Available') {
                return array(\DateTime::createFromFormat('M Y d', $date.' 01'), 'month'); //Month and Year - "Dec 1981" - MAL doesn't seem to use this
            }
        } else { //Date contains a comma
            $dateComponents = explode(' ', $date);
            if (count($dateComponents) == 2) { //Month and Year with comma - "Dec, 1981" - Weird MAL formatting (ex id 21275)
                $month = substr($dateComponents[0], 0, -1); //Remove the comma
                $year = $dateComponents[1];

                return array(\DateTime::createFromFormat('M Y d', $month.' '.$year.' 01'), 'month');
            } elseif (strlen($date) !== 7 && strlen($date) !== 8) { //Full Date. Not sure why we're checking the length here.
                return array(\DateTime::createFromFormat('M j, Y', $date), 'day');
            }
        }

        return null;
    }

    private static function parsePersonalDate(Crawler $content, $type, $startOrFinish)
    {
        //MAL allows users to just submit year, year and month, or all three.
        //The below code is so we can return full dates and make it all standard.
        $extracted = $content->filterXPath('//select[@id="add_'.$type.'_'.$startOrFinish.'_date_year"]/option[@selected]');
        if ($extracted->count() > 0) {
            $year = (int) $extracted->attr('value');

            //Default Values in the case month and day are not already set.
            $month = 6;
            $day = 15;

            //In some cases, MAL doesn't check "unknown" and you get blank values. MAL is weird.
            //Make sure that we have a valid year before continuing.
            if ($year != 0) {
                $extracted = $content->filterXPath('//select[@id="add_'.$type.'_'.$startOrFinish.'_date_month"]/option[@selected]');
                if ($extracted->count() > 0 && $extracted->attr('value') != '') {
                    $month = (int) $extracted->attr('value');

                    $extracted = $content->filterXPath('//select[@id="add_'.$type.'_'.$startOrFinish.'_date_day"]/option[@selected]');
                    if ($extracted->count() > 0 && $extracted->attr('value') != '') {
                        $day = (int) $extracted->attr('value');
                    }
                }

                return \DateTime::createFromFormat('Y-n-j', "$year-$month-$day");
            }

            return;
        }
    }

    public static function parsePics($contents, $type)
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($contents, 'UTF-8');

        $titlePics = array();

        //The main content of the page
        $mainContent = $crawler->filterXPath('//div[@id="content"]/table/tr/td[2]');

        //Grab the gallery image blocks inside the main content
        $picsContent = $mainContent->filterXPath('//div[@class="picSurround"]');

        if (count($picsContent) > 0) {
            foreach ($picsContent as $pic) {
                // Some images are linked to a "large" version.
                // Use linked version if it exists, otherwise use the image itself.
                $picNode = new Crawler($pic);
                $possibleAnchor = $picNode->filterXPath('//a');
                $possibleImage = $picNode->filterXPath('//img');

                if ($possibleAnchor->count() > 0) {
                    $titlePics[] = $possibleAnchor->attr('href');
                } elseif ($possibleImage->count() > 0) {
                    $titlePics[] = $possibleImage->attr('src');
                }
            }
        }

        return $titlePics;
    }
}
