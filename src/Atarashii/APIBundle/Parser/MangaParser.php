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

use Symfony\Component\DomCrawler\Crawler;
use Atarashii\APIBundle\Model\Manga;
use \DateTime;

class MangaParser
{
    public static function parse($contents, $mine = false)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $mangarecord = new Manga();

        # Manga ID.
        # Example:
        # <input type="hidden" value="104" name="mid" />
        $mangarecord->setId((int) $crawler->filter('input[name="mid"]')->attr('value'));

        # Title and rank.
        # Example:
        # <h1>
        #   <div style="float: right; font-size: 13px;">Ranked #8</div>Yotsuba&!
        #   <span style="font-weight: normal;"><small>(Manga)</small></span>
        # </h1>
        $mangarecord->setTitle(trim($title = $crawler->filterXPath('//h1/text()')->text()));
        $mangarecord->setRank((int) str_replace('Ranked #', '', $crawler->filter('h1 div')->text()));

        # Title Image
        # Example:
        # <a href="http://myanimelist.net/manga/104/Yotsubato!/pic&pid=90029"><img src="http://cdn.myanimelist.net/images/manga/4/90029.jpg" alt="Yotsubato!" align="center"></a>
        $mangarecord->setImageUrl($crawler->filter('div#content tr td div img')->attr('src'));

        // Left Column - Alt titles, info, stats, tags
        $leftcolumn = $crawler->filterXPath('//div[@id="content"]/table/tr/td[@class="borderClass"]');

        # Alternative Titles section.
        # Example:
        # <h2>Alternative Titles</h2>
        # <div class="spaceit_pad"><span class="dark_text">English:</span> Yotsuba&!</div>
        # <div class="spaceit_pad"><span class="dark_text">Synonyms:</span> Yotsubato!, Yotsuba and !, Yotsuba!, Yotsubato, Yotsuba and!</div>
        # <div class="spaceit_pad"><span class="dark_text">Japanese:</span> よつばと！</div>

        # English:
        $extracted = $leftcolumn->filterXPath('//span[text()="English:"]');
        if (iterator_count($extracted) > 0) {
            $text = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $setother_titles['english'] = explode(', ', $text);
            $mangarecord->setOtherTitles($setother_titles);
        }

        # Synonyms:
        $extracted = $leftcolumn->filterXPath('//span[text()="Synonyms:"]');
        if (iterator_count($extracted) > 0) {
            $text = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $setother_titles['synonyms'] = explode(', ', $text);
            $mangarecord->setOtherTitles($setother_titles);
        }

        # Japanese:
        $extracted = $leftcolumn->filterXPath('//span[text()="Japanese:"]');
        if (iterator_count($extracted) > 0) {
            $text = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $setother_titles['japanese'] = explode(', ', $text);
            $mangarecord->setOtherTitles($setother_titles);
        }

        # Information section.
        # Example:
        # <h2>Information</h2>
        # <div><span class="dark_text">Type:</span> Manga</div>
        # <div class="spaceit"><span class="dark_text">Volumes:</span> Unknown</div>
        # <div><span class="dark_text">Chapters:</span> Unknown</div>
        # <div class="spaceit"><span class="dark_text">Status:</span> Publishing</div>
        # <div><span class="dark_text">Published:</span> Mar  21, 2003 to ?</div>
        # <div class="spaceit"><span class="dark_text">Genres:</span>
        #   <a href="http://myanimelist.net/manga.php?genre[]=4">Comedy</a>,
        #   <a href="http://myanimelist.net/manga.php?genre[]=36">Slice of Life</a>
        # </div>
        # <div><span class="dark_text">Authors:</span>
        #   <a href="http://myanimelist.net/people/1939/Kiyohiko_Azuma">Azuma, Kiyohiko</a> (Story & Art)
        # </div>
        # <div class="spaceit"><span class="dark_text">Serialization:</span>
        #   <a href="http://myanimelist.net/manga.php?mid=23">Dengeki Daioh (Monthly)</a>
        # </div>

        # Type:
        $extracted = $leftcolumn->filterXPath('//span[text()="Type:"]');
        if (iterator_count($extracted) > 0) {
            $mangarecord->setType(trim(str_replace($extracted->text(), '', $extracted->parents()->text())));
        }

        # Volumes:
        $extracted = $leftcolumn->filterXPath('//span[text()="Volumes:"]');
        if (iterator_count($extracted) > 0) {
            $data = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));

            if ($data != "Unknown") {
                $mangarecord->setVolumes((int) $data);
            } else {
                $mangarecord->setVolumes(null);
            }
        } else {
            $mangarecord->setVolumes(null);
        }

        # Chapters:
        $extracted = $leftcolumn->filterXPath('//span[text()="Chapters:"]');
        if (iterator_count($extracted) > 0) {
            $data = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));

            if ($data != "Unknown") {
                $mangarecord->setChapters((int) $data);
            } else {
                $mangarecord->setChapters(null);
            }
        } else {
            $mangarecord->setChapters(null);
        }

        # Status:
        $extracted = $leftcolumn->filterXPath('//span[text()="Status:"]');
        if (iterator_count($extracted) > 0) {
            $mangarecord->setStatus(strtolower(trim(str_replace($extracted->text(), '', $extracted->parents()->text()))));
        }

        # Genres:
        $extracted = $leftcolumn->filterXPath('//span[text()="Genres:"]');
        if (iterator_count($extracted) > 0) {
            $mangarecord->setGenres(explode(', ', trim(str_replace($extracted->text(), '', $extracted->parents()->text()))));
        }

        # Statistics
        # Example:
        # <h2>Statistics</h2>
        # <div><span class="dark_text">Score:</span> 8.90<sup><small>1</small></sup> <small>(scored by 4899 users)</small>
        # </div>
        # <div class="spaceit"><span class="dark_text">Ranked:</span> #8<sup><small>2</small></sup></div>
        # <div><span class="dark_text">Popularity:</span> #32</div>
        # <div class="spaceit"><span class="dark_text">Members:</span> 8,344</div>
        # <div><span class="dark_text">Favorites:</span> 1,700</div>

        //TODO: Rewrite to properly clean up excess tags.
        # Score:
        $extracted = $leftcolumn->filterXPath('//span[text()="Score:"]');
        if (iterator_count($extracted) > 0) {
            $extracted = str_replace($extracted->text(), '', $extracted->parents()->text());
            //Remove the parenthetical at the end of the string
            $extracted = trim(str_replace(strstr($extracted, '('), '', $extracted));
            //Sometimes there is a superscript number at the end from a note.
            //Scores are only two decimals, so number_format should chop off the excess, hopefully.
            $mangarecord->setMembersScore((float) number_format($extracted, 2));
        }

        # Popularity:
        $extracted = $leftcolumn->filterXPath('//span[text()="Popularity:"]');
        if (iterator_count($extracted) > 0) {
            $extracted = str_replace($extracted->text(), '', $extracted->parents()->text());
            //Remove the hash at the front of the string and trim whitespace. Needed so we can cast to an int.
            $extracted = trim(str_replace('#', '', $extracted));
            $mangarecord->setPopularityRank((int) $extracted);
        }

        # Members:
        $extracted = $leftcolumn->filterXPath('//span[text()="Members:"]');
        if (iterator_count($extracted) > 0) {
            $extracted = str_replace($extracted->text(), '', $extracted->parents()->text());
            //PHP doesn't like commas in integers. Remove it.
            $extracted = trim(str_replace(',', '', $extracted));
            $mangarecord->setMembersCount((int) $extracted);
        }

        # Members:
        $extracted = $leftcolumn->filterXPath('//span[text()="Favorites:"]');
        if (iterator_count($extracted) > 0) {
            $extracted = str_replace($extracted->text(), '', $extracted->parents()->text());
            //PHP doesn't like commas in integers. Remove it.
            $extracted = trim(str_replace(',', '', $extracted));
            $mangarecord->setFavoritedCount((int) $extracted);
        }

        # Popular Tags
        # Example:
        # <h2>Popular Tags</h2>
        # <span style="font-size: 11px;">
        #   <a href="http://myanimelist.net/manga.php?tag=comedy" style="font-size: 24px" title="241 people tagged with comedy">comedy</a>
        #   <a href="http://myanimelist.net/manga.php?tag=slice of life" style="font-size: 11px" title="207 people tagged with slice of life">slice of life</a>
        # </span>
        $extracted = $leftcolumn->filterXPath('//h2[text()="Popular Tags"]')->nextAll()->filter('a');
        foreach ($extracted as $term) {
            $mangarecord->setTags($term->textContent);
        }

        # -
        # Extract from sections on the right column: Synopsis, Related Manga
        # -
        $rightcolumn = $crawler->filterXPath('//div[@id="content"]/table/tr/td/div/table');

        # Synopsis
        # Example:
        # <h2>Synopsis</h2>
        # Yotsuba's daily life is full of adventure. She is energetic, curious, and a bit odd &ndash; odd enough to be called strange by her father as well as ignorant of many things that even a five-year-old should know. Because of this, the most ordinary experience can become an adventure for her. As the days progress, she makes new friends and shows those around her that every day can be enjoyable.<br />
        # <br />
        # [Written by MAL Rewrite]
        $extracted = $rightcolumn->filterXPath('//h2[text()="Synopsis"]');

        //Compatibility Note: We don't convert extended characters to HTML entities, we just
        //use the output directly from MAL. This should be okay as our return charset is UTF-8.
        if (iterator_count($extracted) > 0) {
            $extracted = str_replace($extracted->html(), '', $extracted->parents()->html());
            $extracted = str_replace('<h2></h2>', '', $extracted);
            $mangarecord->setSynopsis($extracted);
        }

        # Related Manga
        # Example:
        # <h2>Related Manga</h2>
        #   Adaptation: <a href="http://myanimelist.net/anime/66/Azumanga_Daioh">Azumanga Daioh</a><br>
        #   Side story: <a href="http://myanimelist.net/manga/13992/Azumanga_Daioh:_Supplementary_Lessons">Azumanga Daioh: Supplementary Lessons</a><br>
        $related = $rightcolumn->filterXPath('//h2[text()="Related Manga"]');

        //TODO: Figure out if there is an easier way to get the content.
        //NOTE: We don't grab "Alternative Setting" or "Other" titles.
        if (iterator_count($related)) {
            //Get all the content between the "Related Anime" h2 and the next h2 tag.
            if (preg_match('/\<h2\>Related Manga\<\/h2\>(.+?)\<h2\>/', $related->parents()->html(), $relatedcontent)) {
                $relatedcontent = $relatedcontent[1];

                #Adaptation
                if (preg_match('/Adaptation\: ?(<a .+?)\<br/', $relatedcontent, $relateditems)) {
                    $relateditems = explode(', ', $relateditems[1]);
                    foreach ($relateditems as $item) {
                        if (preg_match('/<a href="(\/anime\/(\d+)\/.*?)">(.+?)<\/a>/', $item, $itemparts)) {
                            $itemarray = array();
                            $itemarray['anime_id'] = $itemparts[2];
                            $itemarray['title'] = $itemparts[3];
                            $itemarray['url'] = 'http://myanimelist.net'.$itemparts[1];
                            $mangarecord->setAnimeAdaptations($itemarray);
                        }
                    }
                }

                #Related Manga
                #NOTE: This doesn't seem to work as intended, but matches behavior of the Ruby API
                if (preg_match('/.+\: ?(<a .+?)\<br/', $relatedcontent, $relateditems)) {
                    $relateditems = explode(', ', $relateditems[1]);
                    foreach ($relateditems as $item) {
                        if (preg_match('/<a href="(\/manga\/(\d+)\/.*?)">(.+?)<\/a>/', $item, $itemparts)) {
                            $itemarray = array();
                            $itemarray['manga_id'] = $itemparts[2];
                            $itemarray['title'] = $itemparts[3];
                            $itemarray['url'] = 'http://myanimelist.net'.$itemparts[1];
                            $mangarecord->setRelatedManga($itemarray);
                        }
                    }
                }

                #Alternative Versions
                if (preg_match('/Alternative versions?\: ?(<a .+?)\<br/', $relatedcontent, $relateditems)) {
                    $relateditems = explode(', ', $relateditems[1]);
                    foreach ($relateditems as $item) {
                        if (preg_match('/<a href="(\/manga\/(\d+)\/.*?)">(.+?)<\/a>/', $item, $itemparts)) {
                            $itemarray = array();
                            $itemarray['manga_id'] = $itemparts[2];
                            $itemarray['title'] = $itemparts[3];
                            $itemarray['url'] = 'http://myanimelist.net'.$itemparts[1];
                            $mangarecord->setAlternativeVersions($itemarray);
                        }
                    }
                }

                //Note: There is a "related manga" option, but it doesn't appear to
                //work properly in the existing API. We should extend to include all
                //the other relations anyway.
            }
        }

        # User's manga details (only available if he authenticates).
        # <h2>My Info</h2>
        # <div id="addtolist" style="display: block;">
        #   <input type="hidden" id="myinfo_manga_id" value="104">
        #   <table border="0" cellpadding="0" cellspacing="0" width="100%">
        #   <tr>
        #     <td class="spaceit">Status:</td>
        #     <td class="spaceit"><select id="myinfo_status" name="myinfo_status" onchange="checkComp(this);" class="inputtext"><option value="1" selected>Reading</option><option value="2" >Completed</option><option value="3" >On-Hold</option><option value="4" >Dropped</option><option value="6" >Plan to Read</option></select></td>
        #   </tr>
        #   <tr>
        #     <td class="spaceit">Chap. Read:</td>
        #     <td class="spaceit"><input type="text" id="myinfo_chapters" size="3" maxlength="4" class="inputtext" value="62"> / <span id="totalChaps">0</span></td>
        #   </tr>
        #   <tr>
        #     <td class="spaceit">Vol. Read:</td>
        #     <td class="spaceit"><input type="text" id="myinfo_volumes" size="3" maxlength="4" class="inputtext" value="5"> / <span id="totalVols">?</span></td>
        #   </tr>
        #   <tr>
        #     <td class="spaceit">Your Score:</td>
        #     <td class="spaceit"><select id="myinfo_score" name="myinfo_score" class="inputtext"><option value="0">Select</option><option value="10" selected>(10) Masterpiece</option><option value="9" >(9) Great</option><option value="8" >(8) Very Good</option><option value="7" >(7) Good</option><option value="6" >(6) Fine</option><option value="5" >(5) Average</option><option value="4" >(4) Bad</option><option value="3" >(3) Very Bad</option><option value="2" >(2) Horrible</option><option value="1" >(1) Unwatchable</option></select></td>
        #   </tr>
        #   <tr>
        #     <td>&nbsp;</td>
        #     <td><input type="button" name="myinfo_submit" value="Update" onclick="myinfo_updateInfo();" class="inputButton"> <small><a href="http://www.myanimelist.net/panel.php?go=editmanga&id=75054">Edit Details</a></small></td>
        #   </tr>
        #   </table>
        # </div>

        #Read Status - Only available when user is authenticated
        $my_data = $crawler->filter('select#myinfo_status');
        if (iterator_count($my_data) && iterator_count($my_data->filter('option[selected="selected"]'))) {
            $mangarecord->setReadStatus($my_data->filter('option[selected="selected"]')->attr('value'));
        }

        #Read Chapters - Only available when user is authenticated
        $my_data = $crawler->filter('input#myinfo_chapters');
        if (iterator_count($my_data)) {
            $mangarecord->setChaptersRead((int) $my_data->attr('value'));
        }

        #Read Volumes - Only available when user is authenticated
        $my_data = $crawler->filter('input#myinfo_volumes');
        if (iterator_count($my_data)) {
            $mangarecord->setVolumesRead((int) $my_data->attr('value'));
        }

        #User's Score - Only available when user is authenticated
        $my_data = $crawler->filter('select#myinfo_score');
        if (iterator_count($my_data) && iterator_count($my_data->filter('option[selected="selected"]'))) {
            $mangarecord->setScore((int) $my_data->filter('option[selected="selected"]')->attr('value'));
        }

        #Listed ID (?) - Only available when user is authenticated
        $my_data = $crawler->filterXPath('//a[text()="Edit Details"]');
        if (iterator_count($my_data)) {
            if (preg_match('/id=(\d+)/', $my_data->attr('href'), $my_data)) {
                $mangarecord->setListedMangaId((int) $my_data[1]);
            }
        }

        return $mangarecord;
    }

    public static function parseExtendedPersonal($contents, $manga)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        #Re-reading
        #<label><input type="checkbox" name="rereading" id="rereadingBox" onclick="checkReReading();" value="1" > Re-reading</label>
        $rereading = $crawler->filter('input[id="rereadingBox"]')->attr('checked');

        if ($rereading) {
            $manga->setRereading(true);
        }

        #Personal tags
        #<td align="left" class="borderClass"><textarea name="tags" rows="2" id="tagtext" cols="45" class="textarea"></textarea><div class="spaceit_pad"><small>Popular tags: <a href="javascript:void(0);" onclick="detailedadd_addTag('cooking');">cooking</a>, <a href="javascript:void(0);" onclick="detailedadd_addTag('seinen');">seinen</a>, <a href="javascript:void(0);" onclick="detailedadd_addTag('drama');">drama</a>, <a href="javascript:void(0);" onclick="detailedadd_addTag('slice of life');">slice of life</a></small></div></td>
        $personalTags = $crawler->filter('textarea[name="tags"]')->text();

        if (strlen($personalTags) > 0) {
            $personalTags = explode(',', $personalTags);

            foreach ($personalTags as $tag) {
                $tagArray[] = trim($tag);
            }

            $manga->setPersonalTags($tagArray);
        }

        #Start and Finish Dates
        #<tr>
        #    <td align="left" class="borderClass">Start Date</td>
        #                <td align="left" class="borderClass">
        #    Month:
        #    <select name="startMonth" id="smonth"  class="inputtext">
        #        <option value="00">
        #        <option value="01" >Jan<option value="02" >Feb<option value="03" >Mar<option value="04" >Apr<option value="05" >May<option value="06" >Jun<option value="07" >Jul<option value="08" >Aug<option value="09" selected>Sep<option value="10" >Oct<option value="11" >Nov<option value="12" >Dec			</select>
        #    Day:
        #    <select name="startDay"  class="inputtext">
        #        <option value="00">
        #        <option value="01" >1<option value="02" >2<option value="03" >3<option value="04" >4<option value="05" >5<option value="06" >6<option value="07" >7<option value="08" >8<option value="09" >9<option value="10" >10<option value="11" >11<option value="12" >12<option value="13" >13<option value="14" >14<option value="15" >15<option value="16" >16<option value="17" >17<option value="18" >18<option value="19" >19<option value="20" >20<option value="21" >21<option value="22" >22<option value="23" >23<option value="24" >24<option value="25" selected>25<option value="26" >26<option value="27" >27<option value="28" >28<option value="29" >29<option value="30" >30<option value="31" >31			</select>
        #    Year:
        #    <select name="startYear"  class="inputtext">
        #        <option value="0000">
        #        <option value="2014" >2014<option value="2013" selected>2013<option value="2012" >2012<option value="2011" >2011<option value="2010" >2010<option value="2009" >2009<option value="2008" >2008<option value="2007" >2007<option value="2006" >2006<option value="2005" >2005<option value="2004" >2004<option value="2003" >2003<option value="2002" >2002<option value="2001" >2001<option value="2000" >2000<option value="1999" >1999<option value="1998" >1998<option value="1997" >1997<option value="1996" >1996<option value="1995" >1995<option value="1994" >1994<option value="1993" >1993<option value="1992" >1992<option value="1991" >1991<option value="1990" >1990<option value="1989" >1989<option value="1988" >1988<option value="1987" >1987<option value="1986" >1986<option value="1985" >1985<option value="1984" >1984			</select>
        #    &nbsp;
        #    <label><input type="checkbox"  onchange="ChangeStartDate();" name="unknownStart" value="1"> <small>Unknown Date</label><br>Start Date represents the date you started watching the Anime <a href="javascript:setToday(1);">Insert Today</a></small>
        #    </td>
        #</tr>
        #<tr>
        #    <td align="left" class="borderClass">Finish Date</td>
        #                <td align="left" class="borderClass">
        #    Month:
        #    <select name="endMonth" id="emonth" class="inputtext" >
        #        <option value="00">
        #        <option value="01" >Jan<option value="02" >Feb<option value="03" >Mar<option value="04" >Apr<option value="05" >May<option value="06" >Jun<option value="07" >Jul<option value="08" >Aug<option value="09" >Sep<option value="10" selected>Oct<option value="11" >Nov<option value="12" >Dec			</select>
        #    Day:
        #    <select name="endDay" class="inputtext" >
        #        <option value="00">
        #        <option value="01" >1<option value="02" >2<option value="03" >3<option value="04" >4<option value="05" >5<option value="06" >6<option value="07" >7<option value="08" >8<option value="09" >9<option value="10" >10<option value="11" selected>11<option value="12" >12<option value="13" >13<option value="14" >14<option value="15" >15<option value="16" >16<option value="17" >17<option value="18" >18<option value="19" >19<option value="20" >20<option value="21" >21<option value="22" >22<option value="23" >23<option value="24" >24<option value="25" >25<option value="26" >26<option value="27" >27<option value="28" >28<option value="29" >29<option value="30" >30<option value="31" >31			</select>
        #    Year:
        #    <select name="endYear" class="inputtext" >
        #        <option value="0000">
        #        <option value="2014" >2014<option value="2013" selected>2013<option value="2012" >2012<option value="2011" >2011<option value="2010" >2010<option value="2009" >2009<option value="2008" >2008<option value="2007" >2007<option value="2006" >2006<option value="2005" >2005<option value="2004" >2004<option value="2003" >2003<option value="2002" >2002<option value="2001" >2001<option value="2000" >2000<option value="1999" >1999<option value="1998" >1998<option value="1997" >1997<option value="1996" >1996<option value="1995" >1995<option value="1994" >1994<option value="1993" >1993<option value="1992" >1992<option value="1991" >1991<option value="1990" >1990<option value="1989" >1989<option value="1988" >1988<option value="1987" >1987<option value="1986" >1986<option value="1985" >1985<option value="1984" >1984			</select>
        #    &nbsp;
        #    <small><label><input type="checkbox" onchange="ChangeEndDate();"  name="unknownEnd" value="1"> Unknown Date</label><br>Do <u>not</u> fill out the Finish Date unless status is <em>Completed</em> <a href="javascript:setToday(2);">Insert Today</a></small>
        #    </td>
        #</tr>
        $isStarted = $crawler->filter('input[name="unknownStart"]')->attr('checked');
        $isEnded = $crawler->filter('input[name="unknownEnd"]')->attr('checked');

        if ($isStarted != "checked") {
            //So, MAL allows users to put in just years, just years and months, or all three values.
            //This mess here is to try and avoid things breaking.
            if ($crawler->filter('select[name="startYear"] option:selected')->count() > 0) {
                $startYear = $crawler->filter('select[name="startYear"] option:selected')->attr('value');
                $startMonth = 6;
                $startDay = 15;

                if ($crawler->filter('select[name="startMonth"] option:selected')->count() > 0) {
                    $startMonth = $crawler->filter('select[name="startMonth"] option:selected')->attr('value');

                    if ($crawler->filter('select[name="startDay"] option:selected')->count() > 0) {
                        $startDay = $crawler->filter('select[name="startDay"] option:selected')->attr('value');
                    }
                }

                $manga->setReadingStart(DateTime::createFromFormat('Y-n-j', "$startYear-$startMonth-$startDay"));
            }
        }

        if ($isEnded != "checked") {
            //Same here, avoid breaking MAL's allowing of partial dates.
            if ($crawler->filter('select[name="endYear"] option:selected')->count() > 0) {
                $endYear = $crawler->filter('select[name="endYear"] option:selected')->attr('value');
                $endMonth = 6;
                $endDay = 15;

                if ($crawler->filter('select[name="endMonth"] option:selected')->count() > 0) {
                    $endMonth = $crawler->filter('select[name="endMonth"] option:selected')->attr('value');

                    if ($crawler->filter('select[name="endDay"] option:selected')->count() > 0) {
                        $endDay = $crawler->filter('select[name="endDay"] option:selected')->attr('value');
                    }
                }

                $manga->setReadingEnd(DateTime::createFromFormat('Y-n-j', "$endYear-$endMonth-$endDay"));
            }
        }

        #Priority
        #<td align="left" class="borderClass">Priority</td>
        #<td align="left" class="borderClass"><select name="priority" class="inputtext">
        #<option value="0">Select</option>
        #<option value="0" selected>Low<option value="1" >Medium<option value="2" >High                </select>
        #<div style="margin-top 3px;"><small>What is your priority level to read this manga?</small></div></td>
        $priority = $crawler->filter('select[name="priority"] option:selected')->attr('value');
        $manga->setPriority($priority);

        #Chapters Downloaded
		#<td align="left" class="borderClass"><input type="text" class="inputtext" size="4" value="0" id="dChap" name="downloaded_chapters"> <a onclick="incChapDownloadCount();" href="javascript:void(0);">+</a></td>
        $downloaded = $crawler->filter('input[id="dChap"]')->attr('value');

        if ($downloaded > 0) {
            $manga->setChapDownloaded($downloaded);
        }

        #Times Reread
        #<td align="left" class="borderClass"><input type="text" class="inputtext" size="4" value="0" name="times_read">
        $rereadCount = $crawler->filter('input[name="times_read"]')->attr('value');

        if ($rereadCount > 0) {
            $manga->setRereadCount($rereadCount);
        }

        #Reread Value
        #<td align="left" class="borderClass"><select class="inputtext" name="reread_value">
        #	<option value="0">Select reread value</option><option value="1">Very Low</option><option value="2">Low</option><option value="3">Medium</option><option value="4">High</option><option value="5">Very High			</option></select>
        $rereadValue = $crawler->filter('select[name="reread_value"] option:selected');

        if (count($rereadValue)) {
            $manga->setRereadValue($rereadValue->attr('value'));
        }

        #Comments
        #<td align="left" class="borderClass"><textarea class="textarea" cols="45" rows="5" name="comments"></textarea></td>
        $comments = trim($crawler->filter('textarea[name="comments"]')->text());

        if (strlen($comments)) {
            $manga->setPersonalComments($comments);
        }

        return $manga;
    }
}
