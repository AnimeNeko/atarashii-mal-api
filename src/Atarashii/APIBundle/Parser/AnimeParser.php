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
use DateTime;

class AnimeParser
{
    public static function parse($contents, $apiVersion)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $animerecord = new Anime();

        # Anime ID.
        # Example:
        # <input type="hidden" name="aid" value="790">
        $animerecord->setId((int) $crawler->filter('input[name="aid"]')->attr('value'));

        # Title and rank.
        # Example:
        # <span itemprop="name">One Piece</span>
        $animerecord->setTitle(trim($crawler->filter('span[itemprop="name"]')->text()));

        $rank = $crawler->filterXPath('//span[contains(@class, "ranked")]');

        if (count($rank) > 0) {
            $animerecord->setRank((int) str_replace('Ranked #', '', $rank->text()));
        }

        # Title Image
        # Example:
        # <a href="http://myanimelist.net/anime/16353/Love_Lab/pic&pid=50257"><img src="http://cdn.myanimelist.net/images/anime/12/50257.jpg" alt="Love Lab" align="center"></a>
        $animerecord->setImageUrl(str_replace('t.jpg', '.jpg', $crawler->filter('div#content tr td div img')->attr('src')));

        # Alternative Titles section.
        # Example:
        # <h2>Alternative Titles</h2>
        # <div class="spaceit_pad"><span class="dark_text">English:</span> Lucky Star/div>
        # <div class="spaceit_pad"><span class="dark_text">Synonyms:</span> Lucky Star, Raki ☆ Suta</div>
        # <div class="spaceit_pad"><span class="dark_text">Japanese:</span> らき すた</div>
        $leftcolumn = $crawler->filterXPath('//div[@id="content"]/table/tr/td[@class="borderClass"]');

        # English:
        $extracted = $leftcolumn->filterXPath('//span[text()="English:"]');
        if ($extracted->count() > 0) {
            $text = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $other_titles['english'] = explode(', ', $text);
            $animerecord->setOtherTitles($other_titles);
        }

        # Synonyms:
        $extracted = $leftcolumn->filterXPath('//span[text()="Synonyms:"]');
        if ($extracted->count() > 0) {
            $text = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $other_titles['synonyms'] = explode(', ', $text);
            $animerecord->setOtherTitles($other_titles);
        }

        # Japanese:
        $extracted = $leftcolumn->filterXPath('//span[text()="Japanese:"]');
        if ($extracted->count() > 0) {
            $text = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $other_titles['japanese'] = explode(', ', $text);
            $animerecord->setOtherTitles($other_titles);
        }

        # Information section.
        # Example:
        # <h2>Information</h2>
        # <div><span class="dark_text">Type:</span> TV</div>
        # <div class="spaceit"><span class="dark_text">Episodes:</span> 24</div>
        # <div><span class="dark_text">Status:</span> Finished Airing</div>
        # <div class="spaceit"><span class="dark_text">Aired:</span> Apr  9, 2007 to Sep  17, 2007</div>
        # <div>
        #   <span class="dark_text">Producers:</span>
        #   <a href="http://myanimelist.net/anime.php?p=2">Kyoto Animation</a>,
        #   <a href="http://myanimelist.net/anime.php?p=104">Lantis</a>,
        #   <a href="http://myanimelist.net/anime.php?p=262">Kadokawa Pictures USA</a><sup><small>L</small></sup>,
        #   <a href="http://myanimelist.net/anime.php?p=286">Bang Zoom! Entertainment</a>
        # </div>
        # <div class="spaceit">
        #   <span class="dark_text">Genres:</span>
        #   <a href="http://myanimelist.net/anime.php?genre[]=4">Comedy</a>,
        #   <a href="http://myanimelist.net/anime.php?genre[]=20">Parody</a>,
        #   <a href="http://myanimelist.net/anime.php?genre[]=23">School</a>,
        #   <a href="http://myanimelist.net/anime.php?genre[]=36">Slice of Life</a>
        # </div>
        # <div><span class="dark_text">Duration:</span> 24 min. per episode</div>
        # <div class="spaceit"><span class="dark_text">Rating:</span> PG-13 - Teens 13 or older</div>

        # Type:
        $extracted = $leftcolumn->filterXPath('//span[text()="Type:"]');
        if ($extracted->count() > 0) {
            $animerecord->setType(trim(str_replace($extracted->text(), '', $extracted->parents()->text())));
        }

        # Episodes:
        $extracted = $leftcolumn->filterXPath('//span[text()="Episodes:"]');
        if ($extracted->count() > 0) {
            $episodeCount = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));

            if (is_numeric($episodeCount)) {
                $animerecord->setEpisodes((int) $episodeCount);
            }
        }

        # Status:
        $extracted = $leftcolumn->filterXPath('//span[text()="Status:"]');
        if ($extracted->count() > 0) {
            $animerecord->setStatus(strtolower(trim(str_replace($extracted->text(), '', $extracted->parents()->text()))));
        }

        # Aired:
        $extracted = $leftcolumn->filterXPath('//span[text()="Aired:"]');
        if ($extracted->count() > 0) {
            /*
             * NOTE: The Ruby API has a bug where yet-to-air shows that only have one date
             * get that listed as the "end_date", not the "start_date". The code below fixes
             * this and in doing so delibrately breaks compatibility in order to present the
             * data properly.
             */

            $daterange = explode(' to ', trim(str_replace($extracted->text(), '', $extracted->parents()->text())));

            //MAL always provides record dates in US-style format.
            if (strpos($daterange[0], ',') === false) {
                if (strlen($daterange[0]) === 4) {
                    $animerecord->setStartDate(DateTime::createFromFormat('Y m d', $daterange[0].' 01 01'), 'year'); //Example ID 6535 or 9951
                } elseif ($daterange[0] !== 'Not available') {
                    $animerecord->setStartDate(DateTime::createFromFormat('M Y d', $daterange[0].' 01'), 'month'); //Example ID 22535 (check upcoming list)
                }
            } else {
                if (count(explode(' ', $daterange[0])) == 2) { //MAL has been showing a comma with month and year (Jan, 2016), so catch that
                    $dateComponents = explode(' ', $daterange[0]);
                    $month = substr($dateComponents[0], 0, -1);
                    $year = $dateComponents[1];
                    $animerecord->setStartDate(DateTime::createFromFormat('M Y d', $month.' '.$year.' 01'), 'month');
                } elseif (strlen($daterange[0]) !== 7 && strlen($daterange[0]) !== 8) {
                    $animerecord->setStartDate(DateTime::createFromFormat('M j, Y', $daterange[0]), 'day');
                }
            }

            //Series not yet to air won't list a range at all while currently airing series will use a "?"
            //For these, we should return a null
            if (count($daterange) > 1 && $daterange[1] !== '?') {
                //MAL always provides record dates in US-style format.
                //Not all dates are full, so we have to figure out how to properly parse them
                $dateParts = explode(' ', $daterange[1]);

                $firstIsNumber = is_numeric($dateParts[0]);
                $hasComma = strpos($dateParts[0], ',');

                if (count($dateParts) == 3) { //Full date, normal processing
                    $endDate = DateTime::createFromFormat('M j, Y', $daterange[1]);
                    $animerecord->setEndDate($endDate, 'day');
                } elseif (count($dateParts) == 2) { //We only have two parts, figure out what we were given
                    if (($firstIsNumber === false) && ($hasComma !== false)) {
                        //So, it looks like month and year, because MAL adds the comma regardless.
                        $endDate = DateTime::createFromFormat(('M, Y d'), $daterange[1].' 01'); //Example ID 21275
                        $animerecord->setEndDate($endDate, 'month');
                    }
                } else {
                    if (count($dateParts) == 1 && $firstIsNumber) {
                        //Most likely just a year.
                        $endDate = DateTime::createFromFormat('Y m d', $daterange[1].' 01 01');
                        $animerecord->setEndDate($endDate, 'year'); //Example ID 11836
                    }
                }
            }
        }

        # Producers:
        $extracted = $leftcolumn->filterXPath('//span[text()="Producers:"]');
        if (strpos($extracted->parents()->text(), 'None found') === false && $extracted->count() > 0) {
            $records = $extracted->parents()->first()->filter('a');

            foreach ($records as $rItem) {
                $producers[] = $rItem->nodeValue;
            }

            $animerecord->setProducers($producers);
        }

        # Genres:
        $extracted = $leftcolumn->filterXPath('//span[text()="Genres:"]');
        if ($extracted->count() > 0) {
            $genres = array();

            $records = $extracted->parents()->first()->filter('a');

            foreach ($records as $rItem) {
                $genres[] = $rItem->nodeValue;
            }

            if (count($genres) > 0) {
                $animerecord->setGenres($genres);
            }
        }

        # Classification:
        $extracted = $leftcolumn->filterXPath('//span[text()="Rating:"]');
        if ($extracted->count() > 0) {
            $animerecord->setClassification(trim(str_replace($extracted->text(), '', $extracted->parents()->text())));
        }

        # Statistics
        # Example:
        # <h2>Statistics</h2>
        # <div>
        #   <span class="dark_text">Score:</span> 8.41<sup><small>1</small></sup>
        #   <small>(scored by 22601 users)</small>
        # </div>
        # <div class="spaceit"><span class="dark_text">Ranked:</span> #96<sup><small>2</small></sup></div>
        # <div><span class="dark_text">Popularity:</span> #15</div>
        # <div class="spaceit"><span class="dark_text">Members:</span> 36,961</div>
        # <div><span class="dark_text">Favorites:</span> 2,874</div>

        //TODO: Rewrite to properly clean up excess tags.
        # Score:
        $extracted = $leftcolumn->filterXPath('//span[text()="Score:"]');
        if ($extracted->count() > 0) {
            $extracted = str_replace($extracted->text(), '', $extracted->parents()->text());
            //Remove the parenthetical at the end of the string
            $extracted = trim(str_replace(strstr($extracted, '('), '', $extracted));

            //Sometimes there is a superscript number at the end from a note.
            //Scores are only two decimals, so number_format should chop off the excess, hopefully.
            if (strpos($extracted, 'N/A') === false) {
                $animerecord->setMembersScore((float) number_format($extracted, 2));
            }
        }

        # Popularity:
        $extracted = $leftcolumn->filterXPath('//span[text()="Popularity:"]');
        if ($extracted->count() > 0) {
            $extracted = str_replace($extracted->text(), '', $extracted->parents()->text());
            //Remove the hash at the front of the string and trim whitespace. Needed so we can cast to an int.
            $extracted = trim(str_replace('#', '', $extracted));
            $animerecord->setPopularityRank((int) $extracted);
        }

        # Members:
        $extracted = $leftcolumn->filterXPath('//span[text()="Members:"]');
        if ($extracted->count() > 0) {
            $extracted = str_replace($extracted->text(), '', $extracted->parents()->text());
            //PHP doesn't like commas in integers. Remove it.
            $extracted = trim(str_replace(',', '', $extracted));
            $animerecord->setMembersCount((int) $extracted);
        }

        # Members:
        $extracted = $leftcolumn->filterXPath('//span[text()="Favorites:"]');
        if ($extracted->count() > 0) {
            $extracted = str_replace($extracted->text(), '', $extracted->parents()->text());
            //PHP doesn't like commas in integers. Remove it.
            $extracted = trim(str_replace(',', '', $extracted));
            $animerecord->setFavoritedCount((int) $extracted);
        }

        # -
        # Extract from sections on the right column: Synopsis, Related Anime, Characters & Voice Actors, Reviews
        # Recommendations.
        # -
        $rightcolumn = $crawler->filterXPath('//div[@id="content"]/table/tr/td[2]');

        # Synopsis
        # Example:
        # <td>
        # <h2>Synopsis</h2>
        # Having fun in school, doing homework together, cooking and eating, playing videogames, watching anime. All those little things make up the daily life of the anime- and chocolate-loving Izumi Konata and her friends. Sometimes relaxing but more than often simply funny! <br />
        # -From AniDB
        $extracted = $crawler->filterXPath('//span[@itemprop="description"]');

        //Compatibility Note: We don't convert extended characters to HTML entities, we just
        //use the output directly from MAL. This should be okay as our return charset is UTF-8.
        $animerecord->setSynopsis('There is currently no synopsis for this title.');

        if ($extracted->count() > 0) {
            $animerecord->setSynopsis($extracted->html());
        }

        # Related Anime
        # Example:
        #<table class="anime_detail_related_anime" style="border-spacing:0px;">
        #  <tr>
        #    <td class="ar fw-n borderClass" nowrap="" valign="top">Adaptation:</td>
        #    <td class="borderClass" width="100%"><a href="/manga/587/Lucky☆Star">Lucky☆Star</a></td>
        #  </tr>
        #  <tr>
        #    <td class="ar fw-n borderClass" nowrap="" valign="top">Character:</td>
        #    <td class="borderClass" width="100%"><a href="/anime/3080/Anime_Tenchou">Anime Tenchou</a></td>
        #  </tr>
        #</table>

        $related = $rightcolumn->filter('table.anime_detail_related_anime');

        //NOTE: Not all relations are currently supported.
        if (iterator_count($related)) {
            $rows = $related->children();
            foreach ($rows as $row) {
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
                                $itemId = (int)$urlParts[2];
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
                            $itemArray['url'] = 'https://myanimelist.net' . $itemUrl;

                            $animerecord->addRelation($itemArray, $relationType);
                        }
                    }

                    //Grab next item
                    $relatedItem = $relatedItem->nextSibling;
                } while ($relatedItem !== null);
            }
        }

        # Personal Info
        $userPersonalInfo = $crawler->filterXPath('//h2[text()="Edit Status"]');

        // Only try to parse personal info if the box is there
        if ($userPersonalInfo->count() > 0) {
            #Watched Status - Only available when user is authenticated
            $my_data = $crawler->filter('select#myinfo_status');
            if (iterator_count($my_data) && iterator_count($my_data->filter('option[selected="selected"]'))) {
                $animerecord->setWatchedStatus($my_data->filter('option[selected="selected"]')->attr('value'));
            }

            #Watched Episodes - Only available when user is authenticated
            $my_data = $crawler->filter('input#myinfo_watchedeps');
            if (iterator_count($my_data)) {
                $animerecord->setWatchedEpisodes((int) $my_data->attr('value'));
            }

            #User's Score - Only available when user is authenticated
            $my_data = $crawler->filter('select#myinfo_score');
            if (iterator_count($my_data) && iterator_count($my_data->filter('option[selected="selected"]'))) {
                $animerecord->setScore((int) $my_data->filter('option[selected="selected"]')->attr('value'));
            }

            #Listed ID (?) - Only available when user is authenticated
            $my_data = $crawler->filterXPath('//a[text()="Edit Details"]');
            if (iterator_count($my_data)) {
                if (preg_match('/id=(\d+)/', $my_data->attr('href'), $my_data)) {
                    $animerecord->setListedAnimeId((int) $my_data[1]);
                }
            }
        }

        if ($apiVersion >= '2.1') {
            # Background
            preg_match('/div>Background<\/h2>(.+?)<div/s', $crawler->filter('span[itemprop="description"]')->parents()->html(), $matches);
            if (strpos($matches[0], 'No background information') !== false) {
                $animerecord->setBackground('No background information has been added to this title.');
            } else {
                $animerecord->setBackground(trim($matches[1]));
            }

            # Broadcast:
            $extracted = $leftcolumn->filterXPath('//span[text()="Broadcast:"]');
            if ($extracted->count() > 0) {
                $animerecord->setBroadcast(trim(preg_replace('/(\w.+)s at(\s\d.+)\((\w.+)\)/', '$1$2$3', str_replace($extracted->text(), '', $extracted->parents()->text()))));
            }

            # Duration:
            $extracted = $leftcolumn->filterXPath('//span[text()="Duration:"]');
            if ($extracted->count() > 0) {
                $duration = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));

                // Handle varations that include minutes
                if(strpos($duration, 'min.') !== false) {
                    if(strpos($duration, 'hr.') !== false) { //contains hours and minutes
                        preg_match('/([0-9]+) hr\. ([0-9]+) min\./', $duration, $durationParts);
                        //This could all be done in one line, but it's more understandable and maintainable broken up.
                        $hours = (int) $durationParts[1];
                        $minutes = (int) $durationParts[2];
                        $animerecord->setDuration(($hours * 60) + $minutes);
                    } else { //contains only minutes
                        preg_match('/([0-9]+) min\./', $duration, $durationParts);
                        $animerecord->setDuration((int) $durationParts[1]);
                    }
                //Handle hour-only durations
                } elseif (strpos($duration, 'hr.') !== false) {
                    preg_match('/([0-9]+) hr\./', $duration, $durationParts);
                    $animerecord->setDuration((int) $durationParts[1] * 60);
                }
                // Any other format (such as just "Unknown") isn't understood and is ignored
            }

            // External links is only visible when an user has logged in any may be hidden on some records.
            $externalLinks = $crawler->filterXPath('//h2[text()="External Links"]');
            if ($externalLinks->count() > 0) {
                $extracted = $externalLinks->nextAll()->filter('a');
                # External Links:
                foreach ($extracted as $externalLinkRow) {
                    $animerecord->setExternalLinks($externalLinkRow->nodeValue, $externalLinkRow->getAttribute('href'));
                }
            }

            # Preview:
            $extracted = $crawler->filter('div[class="video-promotion"] a');
            if ($extracted->count() > 0) {
                $animerecord->setPreview(preg_replace('/\?(.+?)$/', '$2', $extracted->attr('href')));
            }

            # Opening Theme:
            $extracted = $crawler->filter('div[class="theme-songs js-theme-songs opnening"] span');
            foreach ($extracted as $openingRow) {
                $animerecord->setOpeningTheme($openingRow->nodeValue);
            }

            # Ending Theme:
            $extracted = $crawler->filter('div[class="theme-songs js-theme-songs ending"] span');
            foreach ($extracted as $endingRow) {
                $animerecord->setEndingTheme($endingRow->nodeValue);
            }

            # Recommendations
            $extracted = $crawler->filter('div[id="anime_recommendation"] li[class="btn-anime"]');
            foreach ($extracted as $recommendationsRow) {
                $recommendationsCrawler = new Crawler($recommendationsRow);
                $anime = new Anime();
                
                // ID can be on either side of the hyphen
                $tempId = preg_replace('/.*\//', '',  $recommendationsCrawler->filter('a')->attr('href')); // Put the 2 IDs into $tempId
                $anime->setId(preg_replace('/(-|'.$animerecord->getId().')/', '', $tempId)); // Remove the hyphen and the current anime's id, leaving the desired id
                
                $anime->setTitle($recommendationsCrawler->filter('span')->text());
                $anime->setImageUrl(preg_replace('/r(.+?)\/(.+?)\?(.+?)$/','$2',$recommendationsCrawler->filter('img')->attr('data-src')));
                $animerecord->setRecommendations($anime);
            }
        }

        return $animerecord;
    }

    public static function parseExtendedPersonal($contents, Anime $anime)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        #Personal tags
        #<td class="borderClass"><textarea name="tags" rows="2" id="tagtext" cols="45" class="textarea">action, sci-fi</textarea></td>
        $personalTags = $crawler->filter('textarea[id="add_anime_tags"]')->text();

        if (strlen($personalTags) > 0) {
            $personalTags = explode(',', $personalTags);

            foreach ($personalTags as $tag) {
                $tagArray[] = trim($tag);
            }

            $anime->setPersonalTags($tagArray);
        }

        #Start and Finish Dates
        #<tr>
        #   <td class="borderClass">Start Date</td>
        #               <td class="borderClass">
        #   Month:
        #   <select name="startMonth"  class="inputtext">
        #       <option value="00">
        #       <option value="1" >Jan<option value="2" selected>Feb<option value="3" >Mar<option value="4" >Apr<option value="5" >May<option value="6" >Jun<option value="7" >Jul<option value="8" >Aug<option value="9" >Sep<option value="10" >Oct<option value="11" >Nov<option value="12" >Dec         </select>
        #   Day:
        #   <select name="startDay"  class="inputtext">
        #       <option value="00">
        #       <option value="1" >1<option value="2" selected>2<option value="3" >3<option value="4" >4<option value="5" >5<option value="6" >6<option value="7" >7<option value="8" >8<option value="9" >9<option value="10" >10<option value="11" >11<option value="12" >12<option value="13" >13<option value="14" >14<option value="15" >15<option value="16" >16<option value="17" >17<option value="18" >18<option value="19" >19<option value="20" >20<option value="21" >21<option value="22" >22<option value="23" >23<option value="24" >24<option value="25" >25<option value="26" >26<option value="27" >27<option value="28" >28<option value="29" >29<option value="30" >30<option value="31" >31            </select>
        #   Year:
        #   <select name="startYear"  class="inputtext">
        #       <option value="0000">
        #       <option value="2014" selected>2014<option value="2013" >2013<option value="2012" >2012<option value="2011" >2011<option value="2010" >2010<option value="2009" >2009<option value="2008" >2008<option value="2007" >2007<option value="2006" >2006<option value="2005" >2005<option value="2004" >2004<option value="2003" >2003<option value="2002" >2002<option value="2001" >2001<option value="2000" >2000<option value="1999" >1999<option value="1998" >1998<option value="1997" >1997<option value="1996" >1996<option value="1995" >1995<option value="1994" >1994<option value="1993" >1993<option value="1992" >1992<option value="1991" >1991<option value="1990" >1990<option value="1989" >1989<option value="1988" >1988<option value="1987" >1987<option value="1986" >1986<option value="1985" >1985<option value="1984" >1984          </select>
        #   &nbsp;
        #   <label><input type="checkbox"  onchange="ChangeStartDate();"  name="unknownStart" value="1"> <small>Unknown Date</label><br>Start Date represents the date you started watching the Anime <a href="javascript:setToday(1);">Insert Today</a></small>
        #   </td>
        #</tr>
        #<tr>
        #   <td class="borderClass">Finish Date</td>
        #               <td class="borderClass">
        #   Month:
        #   <select name="endMonth" class="inputtext" disabled>
        #       <option value="00">
        #       <option value="1" >Jan<option value="2" >Feb<option value="3" >Mar<option value="4" >Apr<option value="5" >May<option value="6" >Jun<option value="7" >Jul<option value="8" >Aug<option value="9" >Sep<option value="10" >Oct<option value="11" >Nov<option value="12" >Dec         </select>
        #   Day:
        #   <select name="endDay" class="inputtext" disabled>
        #       <option value="00">
        #       <option value="1" >1<option value="2" >2<option value="3" >3<option value="4" >4<option value="5" >5<option value="6" >6<option value="7" >7<option value="8" >8<option value="9" >9<option value="10" >10<option value="11" >11<option value="12" >12<option value="13" >13<option value="14" >14<option value="15" >15<option value="16" >16<option value="17" >17<option value="18" >18<option value="19" >19<option value="20" >20<option value="21" >21<option value="22" >22<option value="23" >23<option value="24" >24<option value="25" >25<option value="26" >26<option value="27" >27<option value="28" >28<option value="29" >29<option value="30" >30<option value="31" >31            </select>
        #   Year:
        #   <select name="endYear" class="inputtext" disabled>
        #       <option value="0000">
        #       <option value="2014" >2014<option value="2013" >2013<option value="2012" >2012<option value="2011" >2011<option value="2010" >2010<option value="2009" >2009<option value="2008" >2008<option value="2007" >2007<option value="2006" >2006<option value="2005" >2005<option value="2004" >2004<option value="2003" >2003<option value="2002" >2002<option value="2001" >2001<option value="2000" >2000<option value="1999" >1999<option value="1998" >1998<option value="1997" >1997<option value="1996" >1996<option value="1995" >1995<option value="1994" >1994<option value="1993" >1993<option value="1992" >1992<option value="1991" >1991<option value="1990" >1990<option value="1989" >1989<option value="1988" >1988<option value="1987" >1987<option value="1986" >1986<option value="1985" >1985<option value="1984" >1984          </select>
        #   &nbsp;
        #   <small><label><input type="checkbox" onchange="ChangeEndDate();" checked name="unknownEnd" value="1"> Unknown Date</label><br>Do <u>not</u> fill out the Finish Date unless status is <em>Completed</em> <a href="javascript:setToday(2);">Insert Today</a></small>
        #   </td>
        #</tr>
        $isStarted = $crawler->filter('input[id="unknown_start"]')->attr('checked');
        $isEnded = $crawler->filter('input[id="unknown_end"]')->attr('checked');

        if ($isStarted != 'checked') {
            //So, MAL allows users to put in just years, just years and months, or all three values.
            //This mess here is to try and avoid things breaking.
            if ($crawler->filter('select[id="add_anime_start_date_year"] option:selected')->count() > 0) {
                $startYear = $crawler->filter('select[id="add_anime_start_date_year"] option:selected')->attr('value');
                $startMonth = 6;
                $startDay = 15;

                if ($startYear !== '') {
                    if ($crawler->filter('select[id="add_anime_start_date_month"] option:selected')->count() > 0) {
                        $startMonth = $crawler->filter('select[id="add_anime_start_date_month"] option:selected')->attr('value');
                        if ($startMonth === '') {
                            $startMonth = 6;
                        }

                        if ($crawler->filter('select[id="add_anime_start_date_day"] option:selected')->count() > 0) {
                            $startDay = $crawler->filter('select[id="add_anime_start_date_day"] option:selected')->attr('value');
                            if ($startDay === '') {
                                $startDay = 15;
                            }
                        }
                    }

                    $anime->setWatchingStart(DateTime::createFromFormat('Y-n-j', "$startYear-$startMonth-$startDay"));
                }
            }
        }

        if ($isEnded != 'checked') {
            //Same here, avoid breaking MAL's allowing of partial dates.
            if ($crawler->filter('select[id="add_anime_finish_date_year"] option:selected')->count() > 0) {
                $endYear = $crawler->filter('select[id="add_anime_finish_date_year"] option:selected')->attr('value');
                $endMonth = 6;
                $endDay = 15;

                if ($endYear !== '') {
                    if ($crawler->filter('select[id="add_anime_finish_date_month"] option:selected')->count() > 0) {
                        $endMonth = $crawler->filter('select[id="add_anime_finish_date_month"] option:selected')->attr('value');
                        if ($endMonth === '') {
                            $endMonth = 6;
                        }

                        if ($crawler->filter('select[id="add_anime_finish_date_day"] option:selected')->count() > 0) {
                            $endDay = $crawler->filter('select[id="add_anime_finish_date_day"] option:selected')->attr('value');
                            if ($endDay === '') {
                                $endDay = 15;
                            }
                        }
                    }

                    $anime->setWatchingEnd(DateTime::createFromFormat('Y-n-j', "$endYear-$endMonth-$endDay"));
                }
            }
        }

        #Priority
        #<td class="borderClass"><select name="priority" class="inputtext">
        #<option value="0" selected>Low<option value="1" >Medium<option value="2" >High         </select>
        $priority = $crawler->filter('select[id="add_anime_priority"] option:selected')->attr('value');
        $anime->setPriority($priority);

        #Storage
        #
        #<td class="borderClass" align="left"><select name="storage" id="storage" onchange="StorageBooleanCheck(2);" class="inputtext">
        #   <option value="0">Select storage type
        #   <option value="1" >Hard Drive<option value="6" >External HD<option value="7" >NAS<option value="2" >DVD / CD<option value="4" >Retail DVD<option value="5" >VHS<option value="3" >None          </select>
        #<div style="margin: 3px 0px; display: none;" id="StorageDiv">Total <span id="storageDescription">DvD's</span> <input type="text" name="storageVal" id="storageValue" value="0.00" size="4" class="inputtext"></div>
        #</td>

        //Note that if storage isn't defined, nothing will be marked as selected. We thus have to get the value in two stages to avoid raising an exception.
        $storage = $crawler->filter('select[id="add_anime_storage_type"] option:selected');

        if (count($storage)) {
            $anime->setStorage($storage->attr('value'));
        }

        #Storage Value - Either number of discs or size in GB
        #<div style="margin: 3px 0px; display: none;" id="StorageDiv">Total <span id="storageDescription">DvD's</span> <input type="text" name="storageVal" id="storageValue" value="1.00" size="4" class="inputtext"></div>
        $storageval = (float) $crawler->filter('input[id="add_anime_storage_value"]')->attr('value');

        if ($storageval > 0) {
            $anime->setStorageValue($storageval);
        }

        #Rewatched
        #<label><input type="checkbox" id="add_anime_is_rewatching" name="add_anime[is_rewatching]" value="1" checked="checked">
        $rewatch = $crawler->filter('input[id="add_anime_is_rewatching"]')->attr('checked');
        if ($rewatch == null) {
            $anime->setRewatching(false);
        } else {
            $anime->setRewatching(true);
        }

        #Times Rewatched
        #<td class="borderClass"><input type="text" name="list_times_watched" value="0" size="4" class="inputtext">
        $rewatchCount = $crawler->filter('input[id="add_anime_num_watched_times"]')->attr('value');

        if ($rewatchCount > 0) {
            $anime->setRewatchCount($rewatchCount);
        }

        #Rewatch Value
        #<td class="borderClass"><select name="list_rewatch_value" class="inputtext">
        #    <option value="0">Select rewatch value<option  value="1">Very Low<option  value="2">Low<option  value="3">Medium<option  value="4">High<option selected value="5">Very High            </select>
        $rewatchValue = $crawler->filter('select[id="add_anime_rewatch_value"] option:selected');

        if (count($rewatchValue)) {
            $anime->setRewatchValue($rewatchValue->attr('value'));
        }

        #Comments
        #<td class="borderClass"><textarea name="list_comments" rows="5" cols="45" class="textarea"></textarea></td>
        $comments = trim($crawler->filter('textarea[id="add_anime_comments"]')->text());

        if (strlen($comments)) {
            $anime->setPersonalComments($comments);
        }

        return $anime;
    }
}
