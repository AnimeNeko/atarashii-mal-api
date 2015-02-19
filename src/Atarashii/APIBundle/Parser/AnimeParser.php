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
use Atarashii\APIBundle\Model\Anime;
use \DateTime;

class AnimeParser
{
    public static function parse($contents, $mine = false)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $animerecord = new Anime();

        # Anime ID.
        # Example:
        # <input type="hidden" name="aid" value="790">
        $animerecord->id = (int) $crawler->filter('input[name="aid"]')->attr('value');

        # Title and rank.
        # Example:
        # <h1><div style="float: right; font-size: 13px;">Ranked #96</div>Lucky ☆ Star</h1>
        $animerecord->title = str_replace($crawler->filter('h1')->children()->text(), '', $crawler->filter('h1')->text());
        $animerecord->rank = (int) str_replace('Ranked #', '', $crawler->filter('h1 div')->text());

        # Title Image
        # Example:
        # <a href="http://myanimelist.net/anime/16353/Love_Lab/pic&pid=50257"><img src="http://cdn.myanimelist.net/images/anime/12/50257.jpg" alt="Love Lab" align="center"></a>
        $animerecord->image_url = $crawler->filter('div#content tr td div img')->attr('src');

        # Alternative Titles section.
        # Example:
        # <h2>Alternative Titles</h2>
        # <div class="spaceit_pad"><span class="dark_text">English:</span> Lucky Star/div>
        # <div class="spaceit_pad"><span class="dark_text">Synonyms:</span> Lucky Star, Raki ☆ Suta</div>
        # <div class="spaceit_pad"><span class="dark_text">Japanese:</span> らき すた</div>
        $leftcolumn = $crawler->filterXPath('//div[@id="content"]/table/tr/td[@class="borderClass"]');

        # English:
        $extracted = $leftcolumn->filterXPath('//span[text()="English:"]');
        if (iterator_count($extracted) > 0) {
            $text = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $animerecord->other_titles['english'] = explode(', ', $text);
        }

        # Synonyms:
        $extracted = $leftcolumn->filterXPath('//span[text()="Synonyms:"]');
        if (iterator_count($extracted) > 0) {
            $text = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $animerecord->other_titles['synonyms'] = explode(', ', $text);
        }

        # Japanese:
        $extracted = $leftcolumn->filterXPath('//span[text()="Japanese:"]');
        if (iterator_count($extracted) > 0) {
            $text = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
            $animerecord->other_titles['japanese'] = explode(', ', $text);
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
        if (iterator_count($extracted) > 0) {
            $animerecord->type = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
        }

        # Episodes:
        $extracted = $leftcolumn->filterXPath('//span[text()="Episodes:"]');
        if (iterator_count($extracted) > 0) {
            $animerecord->episodes = (int) trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
        } else {
            $animerecord->episodes = null;
        }

        # Status:
        $extracted = $leftcolumn->filterXPath('//span[text()="Status:"]');
        if (iterator_count($extracted) > 0) {
            $animerecord->status = strtolower(trim(str_replace($extracted->text(), '', $extracted->parents()->text())));
        }

        # Aired:
        $extracted = $leftcolumn->filterXPath('//span[text()="Aired:"]');
        if (iterator_count($extracted) > 0) {
            /*
             * NOTE: The Ruby API has a bug where yet-to-air shows that only have one date
             * get that listed as the "end_date", not the "start_date". The code below fixes
             * this and in doing so delibrately breaks compatibility in order to present the
             * data properly.
             */

            $daterange = explode(' to ', trim(str_replace($extracted->text(), '', $extracted->parents()->text())));

            //MAL always provides record dates in US-style format. We export to a non-standard format to keep compatibility with the Ruby API.
            //Sometimes the startdate doesn't contain any day. For compatibility with the Ruby API I must pass a date, dayname and time.
            if (strpos($daterange[0],',') == false) {
                if (strlen($daterange[0]) === 4) {
                    $animerecord->start_date = DateTime::createFromFormat('Y m d', $daterange[0] . ' 01 01')->format('Y');
                } elseif ($daterange[0] !== 'Not available') {
                    $animerecord->start_date = DateTime::createFromFormat('M Y d', $daterange[0] . ' 16')->format('D M d 12:00:00 O Y');
                }
            } else {
                $animerecord->start_date = DateTime::createFromFormat('M j, Y', $daterange[0])->format('D M d 12:00:00 O Y');
            }

            //Series not yet to air won't list a range at all while currently airing series will use a "?"
            //For these, we should return a null
            if (count($daterange) > 1 && $daterange[1] !== '?') {
                //MAL always provides record dates in US-style format. We export to a non-standard format to keep compatibility with the Ruby API.
                if (strlen($daterange[1]) === 4) {
                    $animerecord->end_date = DateTime::createFromFormat('Y m d', $daterange[1] . ' 01 01')->format('Y');
                } elseif (strpos($daterange[1],',') == false) {
                    $animerecord->end_date = DateTime::createFromFormat('M Y d', $daterange[1] . ' 16')->format('D M d 12:00:00 O Y');
                } else {
                    $animerecord->end_date = DateTime::createFromFormat('M j, Y', $daterange[1])->format('D M d 12:00:00 O Y');
                }
            }
        }

        # Genres:
        $extracted = $leftcolumn->filterXPath('//span[text()="Genres:"]');
        if (iterator_count($extracted) > 0) {
            $animerecord->genres = explode(', ', trim(str_replace($extracted->text(), '', $extracted->parents()->text())));
        }

        # Classification:
        $extracted = $leftcolumn->filterXPath('//span[text()="Rating:"]');
        if (iterator_count($extracted) > 0) {
            $animerecord->classification = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
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
        if (iterator_count($extracted) > 0) {
            $extracted = str_replace($extracted->text(), '', $extracted->parents()->text());
            //Remove the parenthetical at the end of the string
            $extracted = trim(str_replace(strstr($extracted, '('), '', $extracted));
            //Sometimes there is a superscript number at the end from a note.
            //Scores are only two decimals, so number_format should chop off the excess, hopefully.
            $animerecord->members_score = (float) number_format($extracted, 2);
        }

        # Popularity:
        $extracted = $leftcolumn->filterXPath('//span[text()="Popularity:"]');
        if (iterator_count($extracted) > 0) {
            $extracted = str_replace($extracted->text(), '', $extracted->parents()->text());
            //Remove the hash at the front of the string and trim whitespace. Needed so we can cast to an int.
            $extracted = trim(str_replace('#', '', $extracted));
            $animerecord->popularity_rank = (int) $extracted;
        }

        # Members:
        $extracted = $leftcolumn->filterXPath('//span[text()="Members:"]');
        if (iterator_count($extracted) > 0) {
            $extracted = str_replace($extracted->text(), '', $extracted->parents()->text());
            //PHP doesn't like commas in integers. Remove it.
            $extracted = trim(str_replace(',', '', $extracted));
            $animerecord->members_count = (int) $extracted;
        }

        # Members:
        $extracted = $leftcolumn->filterXPath('//span[text()="Favorites:"]');
        if (iterator_count($extracted) > 0) {
            $extracted = str_replace($extracted->text(), '', $extracted->parents()->text());
            //PHP doesn't like commas in integers. Remove it.
            $extracted = trim(str_replace(',', '', $extracted));
            $animerecord->favorited_count = (int) $extracted;
        }

        # Popular Tags
        # Example:
        # <h2>Popular Tags</h2>
        # <span style="font-size: 11px;">
        #   <a href="http://myanimelist.net/anime.php?tag=comedy" style="font-size: 24px" title="1059 people tagged with comedy">comedy</a>
        #   <a href="http://myanimelist.net/anime.php?tag=parody" style="font-size: 11px" title="493 people tagged with parody">parody</a>
        #   <a href="http://myanimelist.net/anime.php?tag=school" style="font-size: 12px" title="546 people tagged with school">school</a>
        #   <a href="http://myanimelist.net/anime.php?tag=slice of life" style="font-size: 18px" title="799 people tagged with slice of life">slice of life</a>
        # </span>
        $extracted = $leftcolumn->filterXPath('//h2[text()="Popular Tags"]')->nextAll()->filter('a');
        foreach ($extracted as $term) {
            $animerecord->tags[] = $term->textContent;
        }

        # -
        # Extract from sections on the right column: Synopsis, Related Anime, Characters & Voice Actors, Reviews
        # Recommendations.
        # -
        $rightcolumn = $crawler->filterXPath('//div[@id="content"]/table/tr/td/div/table');

        # Synopsis
        # Example:
        # <td>
        # <h2>Synopsis</h2>
        # Having fun in school, doing homework together, cooking and eating, playing videogames, watching anime. All those little things make up the daily life of the anime- and chocolate-loving Izumi Konata and her friends. Sometimes relaxing but more than often simply funny! <br />
        # -From AniDB
        $extracted = $rightcolumn->filterXPath('//h2[text()="Synopsis"]');

        //Compatibility Note: We don't convert extended characters to HTML entities, we just
        //use the output directly from MAL. This should be okay as our return charset is UTF-8.
        if (iterator_count($extracted) > 0) {
            //Grab the whole containing TD that the synopsis is in.
            $extracted = $extracted->parents()->first();
            $advert = $extracted->filterXPath('//div');

            $extracted = str_replace($advert->html(), '', $extracted->html());
            $extracted = str_replace('<h2>Synopsis</h2>', '', $extracted);

            //Ugly regular expression to remove the empty div at the end that used to contain the ad
            $extracted = preg_replace('/\<div(.*?)\<\/div\>$/', '', $extracted);

            $animerecord->synopsis = $extracted;
        }

        # Related Anime
        # Example:
        # <td>
        #   <br>
        #   <h2>Related Anime</h2>
        #   Adaptation: <a href="http://myanimelist.net/manga/9548/Higurashi_no_Naku_Koro_ni_Kai_Minagoroshi-hen">Higurashi no Naku Koro ni Kai Minagoroshi-hen</a>,
        #   <a href="http://myanimelist.net/manga/9738/Higurashi_no_Naku_Koro_ni_Matsuribayashi-hen">Higurashi no Naku Koro ni Matsuribayashi-hen</a><br>
        #   Prequel: <a href="http://myanimelist.net/anime/934/Higurashi_no_Naku_Koro_ni">Higurashi no Naku Koro ni</a><br>
        #   Sequel: <a href="http://myanimelist.net/anime/3652/Higurashi_no_Naku_Koro_ni_Rei">Higurashi no Naku Koro ni Rei</a><br>
        #   Side story: <a href="http://myanimelist.net/anime/6064/Higurashi_no_Naku_Koro_ni_Kai_DVD_Specials">Higurashi no Naku Koro ni Kai DVD Specials</a><br>
        $related = $rightcolumn->filterXPath('//h2[text()="Related Anime"]');

        //TODO: Figure out if there is an easier way to get the content.
        //NOTE: We don't grab "Alternative Setting" or "Other" titles.
        if (iterator_count($related)) {
            //Get all the content between the "Related Anime" h2 and the next h2 tag.
            if (preg_match('/\<h2\>Related Anime\<\/h2\>(.+?)\<h2\>/', $related->parents()->html(), $relatedcontent)) {
                $relatedcontent = $relatedcontent[1];

                #Adaptation
                if (preg_match('/Adaptation\: ?(<a .+?)\<br/', $relatedcontent, $relateditems)) {
                    $relateditems = explode(', ', $relateditems[1]);
                    foreach ($relateditems as $item) {
                        if (preg_match('/<a href="(\/manga\/(\d+)\/.*?)">(.+?)<\/a>/', $item, $itemparts)) {
                            $itemarray = array();
                            $itemarray['manga_id'] = $itemparts[2];
                            $itemarray['title'] = $itemparts[3];
                            $itemarray['url'] = 'http://myanimelist.net'.$itemparts[1];
                            $animerecord->manga_adaptations[] = $itemarray;
                        }
                    }
                }

                #Prequel
                if (preg_match('/Prequel\: ?(<a .+?)\<br/', $relatedcontent, $relateditems)) {
                    $relateditems = explode(', ', $relateditems[1]);
                    foreach ($relateditems as $item) {
                        if (preg_match('/<a href="(\/anime\/(\d+)\/.*?)">(.+?)<\/a>/', $item, $itemparts)) {
                            $itemarray = array();
                            $itemarray['anime_id'] = $itemparts[2];
                            $itemarray['title'] = $itemparts[3];
                            $itemarray['url'] = 'http://myanimelist.net'.$itemparts[1];
                            $animerecord->prequels[] = $itemarray;
                        }
                    }
                }

                #Sequel
                if (preg_match('/Sequel\: ?(<a .+?)\<br/', $relatedcontent, $relateditems)) {
                    $relateditems = explode(', ', $relateditems[1]);
                    foreach ($relateditems as $item) {
                        if (preg_match('/<a href="(\/anime\/(\d+)\/.*?)">(.+?)<\/a>/', $item, $itemparts)) {
                            $itemarray = array();
                            $itemarray['anime_id'] = $itemparts[2];
                            $itemarray['title'] = $itemparts[3];
                            $itemarray['url'] = 'http://myanimelist.net'.$itemparts[1];
                            $animerecord->sequels[] = $itemarray;
                        }
                    }
                }

                #Side story
                if (preg_match('/Side story\: ?(<a .+?)\<br/', $relatedcontent, $relateditems)) {
                    $relateditems = explode(', ', $relateditems[1]);
                    foreach ($relateditems as $item) {
                        if (preg_match('/<a href="(\/anime\/(\d+)\/.*?)">(.+?)<\/a>/', $item, $itemparts)) {
                            $itemarray = array();
                            $itemarray['anime_id'] = $itemparts[2];
                            $itemarray['title'] = $itemparts[3];
                            $itemarray['url'] = 'http://myanimelist.net'.$itemparts[1];
                            $animerecord->side_stories[] = $itemarray;
                        }
                    }
                }

                #Parent story
                if (preg_match('/Parent story\: ?(<a .+?)\<br/', $relatedcontent, $relateditems)) {
                    $relateditems = explode(', ', $relateditems[1]);
                    foreach ($relateditems as $item) {
                        if (preg_match('/<a href="(\/anime\/(\d+)\/.*?)">(.+?)<\/a>/', $item, $itemparts)) {
                            $itemarray = array();
                            $itemarray['anime_id'] = $itemparts[2];
                            $itemarray['title'] = $itemparts[3];
                            $itemarray['url'] = 'http://myanimelist.net'.$itemparts[1];
                            $animerecord->parent_story = $itemarray;
                        }
                    }
                }

                #Character
                if (preg_match('/Character\: ?(<a .+?)\<br/', $relatedcontent, $relateditems)) {
                    $relateditems = explode(', ', $relateditems[1]);
                    foreach ($relateditems as $item) {
                        if (preg_match('/<a href="(\/anime\/(\d+)\/.*?)">(.+?)<\/a>/', $item, $itemparts)) {
                            $itemarray = array();
                            $itemarray['anime_id'] = $itemparts[2];
                            $itemarray['title'] = $itemparts[3];
                            $itemarray['url'] = 'http://myanimelist.net'.$itemparts[1];
                            $animerecord->character_anime[] = $itemarray;
                        }
                    }
                }

                #Spin-off
                if (preg_match('/Spin-off\: ?(<a .+?)\<br/', $relatedcontent, $relateditems)) {
                    $relateditems = explode(', ', $relateditems[1]);
                    foreach ($relateditems as $item) {
                        if (preg_match('/<a href="(\/anime\/(\d+)\/.*?)">(.+?)<\/a>/', $item, $itemparts)) {
                            $itemarray = array();
                            $itemarray['anime_id'] = $itemparts[2];
                            $itemarray['title'] = $itemparts[3];
                            $itemarray['url'] = 'http://myanimelist.net'.$itemparts[1];
                            $animerecord->spin_offs[] = $itemarray;
                        }
                    }
                }

                #Summary
                if (preg_match('/Summary\: ?(<a .+?)\<br/', $relatedcontent, $relateditems)) {
                    $relateditems = explode(', ', $relateditems[1]);
                    foreach ($relateditems as $item) {
                        if (preg_match('/<a href="(\/anime\/(\d+)\/.*?)">(.+?)<\/a>/', $item, $itemparts)) {
                            $itemarray = array();
                            $itemarray['anime_id'] = $itemparts[2];
                            $itemarray['title'] = $itemparts[3];
                            $itemarray['url'] = 'http://myanimelist.net'.$itemparts[1];
                            $animerecord->summaries[] = $itemarray;
                        }
                    }
                }

                #Alternative Versions
                if (preg_match('/Alternative versions?\: ?(<a .+?)\<br/', $relatedcontent, $relateditems)) {
                    $relateditems = explode(', ', $relateditems[1]);
                    foreach ($relateditems as $item) {
                        if (preg_match('/<a href="(\/anime\/(\d+)\/.*?)">(.+?)<\/a>/', $item, $itemparts)) {
                            $itemarray = array();
                            $itemarray['anime_id'] = $itemparts[2];
                            $itemarray['title'] = $itemparts[3];
                            $itemarray['url'] = 'http://myanimelist.net'.$itemparts[1];
                            $animerecord->alternative_versions[] = $itemarray;
                        }
                    }
                }

            }
        }

        # <h2>My Info</h2>
        # <a name="addtolistanchor"></a>
        # <div id="addtolist" style="display: block;">
        #   <input type="hidden" id="myinfo_anime_id" value="934">
        #   <input type="hidden" id="myinfo_curstatus" value="2">
        #
        #   <table border="0" cellpadding="0" cellspacing="0" width="100%">
        #     <tr>
        #       <td class="spaceit">Status:</td>
        #       <td class="spaceit"><select id="myinfo_status" name="myinfo_status" onchange="checkEps(this);" class="inputtext"><option value="1" selected>Watching</option><option value="2" >Completed</option><option value="3" >On-Hold</option><option value="4" >Dropped</option><option value="6" >Plan to Watch</option></select></td>
        #     </tr>
        #     <tr>
        #       <td class="spaceit">Eps Seen:</td>
        #       <td class="spaceit"><input type="text" id="myinfo_watchedeps" name="myinfo_watchedeps" size="3" class="inputtext" value="26"> / <span id="curEps">26</span></td>
        #     </tr>
        #     <tr>
        #       <td class="spaceit">Your Score:</td>
        #         <td class="spaceit"><select id="myinfo_score" name="myinfo_score" class="inputtext"><option value="0">Select</option><option value="10" >(10) Masterpiece</option><option value="9" >(9) Great</option><option value="8" >(8) Very Good</option><option value="7" >(7) Good</option><option value="6" >(6) Fine</option><option value="5" >(5) Average</option><option value="4" >(4) Bad</option><option value="3" >(3) Very Bad</option><option value="2" >(2) Horrible</option><option value="1" >(1) Unwatchable</option></select></td>
        #     </tr>
        #     <tr>
        #       <td>&nbsp;</td>
        #       <td><input type="button" name="myinfo_submit" value="Update" onclick="myinfo_updateInfo(1100070);" class="inputButton"> <small><a href="http://www.myanimelist.net/panel.php?go=edit&id=1100070">Edit Details</a></small></td>
        #     </tr>
        #   </table>

        #Watched Status - Only available when user is authenticated
        $my_data = $crawler->filter('select#myinfo_status');
        if (iterator_count($my_data) && iterator_count($my_data->filter('option[selected="selected"]'))) {
            $animerecord->setWatchedStatus($my_data->filter('option[selected="selected"]')->attr('value'));
        }

        #Watched Episodes - Only available when user is authenticated
        $my_data = $crawler->filter('input#myinfo_watchedeps');
        if (iterator_count($my_data)) {
            $animerecord->watched_episodes = (int) $my_data->attr('value');
        }

        #User's Score - Only available when user is authenticated
        $my_data = $crawler->filter('select#myinfo_score');
        if (iterator_count($my_data) && iterator_count($my_data->filter('option[selected="selected"]'))) {
            $animerecord->score = (int) $my_data->filter('option[selected="selected"]')->attr('value');
        }

        #Listed ID (?) - Only available when user is authenticated
        $my_data = $crawler->filterXPath('//a[text()="Edit Details"]');
        if (iterator_count($my_data)) {
            if (preg_match('/id=(\d+)/', $my_data->attr('href'), $my_data)) {
                $animerecord->listed_anime_id = (int) $my_data[1];
            }
        }

        return $animerecord;
    }
}
