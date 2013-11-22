<?php
namespace Atarashii\APIBundle\Parser;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;
use Atarashii\APIBundle\Model\Profile;
use \SimpleXMLElement;
use \DateTime;

class User {

	public static function parse($contents) {

		$user = new Profile();

		$crawler = new Crawler();
		$crawler->addHTMLContent($contents, 'UTF-8');

		$leftside = $crawler->filter('#content .profile_leftcell');

		$user->avatar_url = $leftside->filter('img')->attr('src');

		$maincontent = iterator_to_array($crawler->filter('#horiznav_nav')->nextAll()->filterXPath('./div/table/tr/td'));

		$userdetails = $maincontent[0];
		$animestats = $maincontent[2];
		$mangastats = $maincontent[3];

		$user->details = self::parseDetails($userdetails, $user->details); //Details is an object, so we need to pass it to the function.
		$user->anime_stats = self::parseStats($animestats);
		$user->manga_stats = self::parseStats($mangastats);

		return $user;
	}

	private static function parseDetails($content, $details) {

		$elements = new Crawler($content);
		$elements = $elements->filter('tr');

		foreach ($elements as $content) {

			$crawler = new Crawler($content);
			$crawler = $crawler->filter('td');

			$values = iterator_to_array($crawler);

			$key = trim(str_replace(' ', '_', strtolower($values[0]->textContent)));
			$value = trim($values[1]->textContent);

			//We have to do some casting and manipulation for certain values so we return them as the right type
			switch ($key) {
				case 'forum_posts':
				case 'manga_list_views':
				case 'anime_list_views':
				case 'comments':
					$value = (int) $value;
					break;
				case 'website':
					//Display value is truncated if it's too long, so get the href value instead.
					$value = $values[1]->firstChild->getAttribute('href');
					break;
			}

			if(property_exists($details, $key)) {
				$details->$key = $value;
			}

		}

		return $details;
	}

	private static function parseStats($content) {
		$stats = array();

		$elements = new Crawler($content);
		$elements = $elements->filter('tr');

		foreach ($elements as $content) {

			$crawler = new Crawler($content);
			$crawler = $crawler->filter('td');

			$values = iterator_to_array($crawler);

			$key = trim($values[0]->textContent);
			$value = trim($values[1]->textContent);

			//Some of the key values have parenthesis. This is messy, but we need to
			//extract only letters to properly transform the names for our output.
			//The regex was found at http://stackoverflow.com/questions/16426976
			$key = trim(preg_replace('~[^\p{L}]++~u', ' ', $values[0]->textContent));
			$key = str_replace(' ', '_', strtolower($key));

			$value = trim($values[1]->textContent);

			$stats[$key] = (float) $value;
		}

		return $stats;
	}

	public static function parseFriends($contents) {
		$crawler = new Crawler();
		$crawler->addHTMLContent($contents, 'UTF-8');
		$maincontent = $crawler->filter('.friendHolder');

		//Empty array so we return something non-null if the list is empty.
		$friendlist = array();

		foreach($maincontent as $friendentry) {
			$crawler = new Crawler($friendentry);

			//All the data extraction.
			$friendavatar = $crawler->filter('.friendIcon')->filterXPath('./div/a/img');
			$friendname = $crawler->filterXPath('//div[@class="friendBlock"]/div[2]/a')->text();
			$lastonline = $crawler->filterXPath('./div/div/div[3]')->text();
			$friendssince = str_replace('Friends since ', '', $crawler->filterXPath('./div/div/div[4]')->text());

			//Remove the tumbnail portions from the URL to get the full image.
			$friendavatar = str_replace('thumbs/', '', str_replace('_thumb', '', $friendavatar->attr('src')));

			//Sometimes this value doesn't exist, so it should be set as null. Otherwise, format the time to RFC3389.
			if($friendssince != '') {
				$friendssince = DateTime::createFromFormat('m-d-y, g:i A', $friendssince)->format(DateTime::RFC3339);
			}
			else {
				$friendssince = null;
			}

			$friendlist[$friendname]['avatar_url'] = $friendavatar;
			$friendlist[$friendname]['last_online'] = $lastonline;
			$friendlist[$friendname]['friend_since'] = $friendssince;
		}
		return $friendlist;

	}
}