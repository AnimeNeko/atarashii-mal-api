<?php
namespace Atarashii\APIBundle\Parser;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;
use Atarashii\APIBundle\Model\Manga;

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
		$mangarecord->id = (int) $crawler->filter('input[name="mid"]')->attr('value');

		# Title and rank.
		# Example:
		# <h1>
		#   <div style="float: right; font-size: 13px;">Ranked #8</div>Yotsuba&!
		#   <span style="font-weight: normal;"><small>(Manga)</small></span>
		# </h1>
		$mangarecord->title = trim($title = $crawler->filterXPath('//h1/text()')->text());
		$mangarecord->rank = (int) str_replace('Ranked #', '', $crawler->filter('h1 div')->text());

		# Title Image
		# Example:
		# <a href="http://myanimelist.net/manga/104/Yotsubato!/pic&pid=90029"><img src="http://cdn.myanimelist.net/images/manga/4/90029.jpg" alt="Yotsubato!" align="center"></a>
		$mangarecord->image_url = $crawler->filter('div#content tr td div img')->attr('src');

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
			$mangarecord->other_titles['english'] = explode(', ', $text);
		}

		# Synonyms:
		$extracted = $leftcolumn->filterXPath('//span[text()="Synonyms:"]');
		if (iterator_count($extracted) > 0) {
			$text = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
			$mangarecord->other_titles['synonyms'] = explode(', ', $text);
		}

		# Japanese:
		$extracted = $leftcolumn->filterXPath('//span[text()="Japanese:"]');
		if (iterator_count($extracted) > 0) {
			$text = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
			$mangarecord->other_titles['japanese'] = explode(', ', $text);
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
			$mangarecord->type = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));
		}

		# Volumes:
		$extracted = $leftcolumn->filterXPath('//span[text()="Volumes:"]');
		if (iterator_count($extracted) > 0) {
			$data = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));

			if ($data != "Unknown") {
				$mangarecord->volumes = (int) $data;
			} else {
				$mangarecord->volumes = null;
			}
		} else {
			$mangarecord->volumes = null;
		}

		# Chapters:
		$extracted = $leftcolumn->filterXPath('//span[text()="Chapters:"]');
		if (iterator_count($extracted) > 0) {
			$data = trim(str_replace($extracted->text(), '', $extracted->parents()->text()));

			if ($data != "Unknown") {
				$mangarecord->chapters = (int) $data;
			} else {
				$mangarecord->chapters = null;
			}
		} else {
			$mangarecord->chapters = null;
		}

		# Status:
		$extracted = $leftcolumn->filterXPath('//span[text()="Status:"]');
		if (iterator_count($extracted) > 0) {
			$mangarecord->status = strtolower(trim(str_replace($extracted->text(), '', $extracted->parents()->text())));
		}

		# Genres:
		$extracted = $leftcolumn->filterXPath('//span[text()="Genres:"]');
		if (iterator_count($extracted) > 0) {
			$mangarecord->genres = explode(', ', trim(str_replace($extracted->text(), '', $extracted->parents()->text())));
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
			$mangarecord->members_score = (float) number_format($extracted, 2);
		}

		# Popularity:
		$extracted = $leftcolumn->filterXPath('//span[text()="Popularity:"]');
		if (iterator_count($extracted) > 0) {
			$extracted = str_replace($extracted->text(), '', $extracted->parents()->text());
			//Remove the hash at the front of the string and trim whitespace. Needed so we can cast to an int.
			$extracted = trim(str_replace('#', '', $extracted));
			$mangarecord->popularity_rank = (int) $extracted;
		}

		# Members:
		$extracted = $leftcolumn->filterXPath('//span[text()="Members:"]');
		if (iterator_count($extracted) > 0) {
			$extracted = str_replace($extracted->text(), '', $extracted->parents()->text());
			//PHP doesn't like commas in integers. Remove it.
			$extracted = trim(str_replace(',', '', $extracted));
			$mangarecord->members_count = (int) $extracted;
		}

		# Members:
		$extracted = $leftcolumn->filterXPath('//span[text()="Favorites:"]');
		if (iterator_count($extracted) > 0) {
			$extracted = str_replace($extracted->text(), '', $extracted->parents()->text());
			//PHP doesn't like commas in integers. Remove it.
			$extracted = trim(str_replace(',', '', $extracted));
			$mangarecord->favorited_count = (int) $extracted;
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
        	$mangarecord->tags[] = $term->textContent;
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
			$mangarecord->synopsis = $extracted;
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
						if (preg_match('/<a href="(http:\/\/myanimelist.net\/anime\/(\d+)\/.*?)">(.+?)<\/a>/', $item, $itemparts)) {
							$itemarray = array();
							$itemarray['manga_id'] = $itemparts[2];
							$itemarray['title'] = $itemparts[3];
							$itemarray['url'] = $itemparts[1];
							$mangarecord->anime_adaptations[] = $itemarray;
						}
					}
				}

				#Related Manga
				#NOTE: This doesn't seem to work as intended, but matches behavior of the Ruby API
				if (preg_match('/.+\: ?(<a .+?)\<br/', $relatedcontent, $relateditems)) {
					$relateditems = explode(', ', $relateditems[1]);
					foreach ($relateditems as $item) {
						if (preg_match('/<a href="(http:\/\/myanimelist.net\/manga\/(\d+)\/.*?)">(.+?)<\/a>/', $item, $itemparts)) {
							$itemarray = array();
							$itemarray['manga_id'] = $itemparts[2];
							$itemarray['title'] = $itemparts[3];
							$itemarray['url'] = $itemparts[1];
							$mangarecord->related_manga[] = $itemarray;
						}
					}
				}

				#Alternative Versions
				if (preg_match('/Alternative versions?\: ?(<a .+?)\<br/', $relatedcontent, $relateditems)) {
					$relateditems = explode(', ', $relateditems[1]);
					foreach ($relateditems as $item) {
						if (preg_match('/<a href="(http:\/\/myanimelist.net\/manga\/(\d+)\/.*?)">(.+?)<\/a>/', $item, $itemparts)) {
							$itemarray = array();
							$itemarray['anime_id'] = $itemparts[2];
							$itemarray['title'] = $itemparts[3];
							$itemarray['url'] = $itemparts[1];
							$mangarecord->alternative_versions[] = $itemarray;
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
			$mangarecord->chapters_read = (int) $my_data->attr('value');
		}

		#Read Volumes - Only available when user is authenticated
		$my_data = $crawler->filter('input#myinfo_volumes');
		if (iterator_count($my_data)) {
			$mangarecord->volumes_read = (int) $my_data->attr('value');
		}

		#User's Score - Only available when user is authenticated
		$my_data = $crawler->filter('select#myinfo_score');
		if (iterator_count($my_data) && iterator_count($my_data->filter('option[selected="selected"]'))) {
			$mangarecord->score = (int) $my_data->filter('option[selected="selected"]')->attr('value');
		}

		#Listed ID (?) - Only available when user is authenticated
		$my_data = $crawler->filterXPath('//a[text()="Edit Details"]');
		if (iterator_count($my_data)) {
			if (preg_match('/id=(\d+)/', $my_data->attr('href'), $my_data)) {
				$mangarecord->listed_manga_id = (int) $my_data[1];
			}
		}

		return $mangarecord;
	}
}
