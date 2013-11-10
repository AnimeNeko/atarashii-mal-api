<?php
namespace Atarashii\APIBundle\Model;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

class User {
	public $avatar_url; //URL to user's avatar (This should be under details, not out here, but Ruby API does it this way)
	public $details; //user's general (not anime/manga-specific) details.
	public $anime_stats; //user's anime statistics.
	public $manga_stats; //user's manga statistics.

	//We set the types of the values to the correct ones that we need for output.
	function __construct() {
		$this->details = new \stdClass();
		$this->anime_stats = new \stdClass();
		$this->manga_stats = new \stdClass();
	}

	public function parse($contents) {

		$crawler = new Crawler();
		$crawler->addHTMLContent($contents, 'UTF-8');

		$leftside = $crawler->filter('#content .profile_leftcell');

		$this->avatar_url = $leftside->filter('img')->attr('src');

		$maincontent = $crawler->filter('#horiznav_nav')->nextAll();

		$this->details = $this->parseDetails($maincontent);
		$this->anime_stats = $this->parseStats($maincontent, 'anime');
		$this->manga_stats = $this->parseStats($maincontent, 'manga');
	}

	private function parseDetails($content) {

		$details = new UserDetails();

		//XPath was found by trial and error. Be careful if you change it.
		$elements = $content->filterXPath('//table[1]//table[1]')->filter('tr');

		foreach ($elements as $content) {

			$crawler = new Crawler($content);

			$key = trim(str_replace(' ', '_', strtolower($crawler->filter('td')->text())));
			$value = trim($crawler->filter('td')->siblings()->text());

			//We have to do some casting and manipulation for certain values so we return them as the right type
			switch ($key) {
				case 'forum_posts':
				case 'manga_list_views':
				case 'anime_list_views':
				case 'comments':
					$value = (int) $value;
					break;
				case 'website':
					$value = 'http://' . $value;
					break;
			}

			$details->$key = $value;
		}

		return $details;
	}

	private function parseStats($content, $type) {

		switch($type) {
			case "anime":
				$elements = $content->filterXPath('//table[4]//table[1]')->filter('tr');
				break;
			case "manga":
				$elements = $content->filterXPath('//table[4]//table[2]')->filter('tr');
				break;
		}

		foreach ($elements as $content) {

			$crawler = new Crawler($content);

			//Some of the key values have parenthesis. This is messy, but we need to
			//extract only letters to properly transform the names for our output.
			//The regex was found at http://stackoverflow.com/questions/16426976
			$key = $crawler->filter('td')->text();
			$key = trim(preg_replace('~[^\p{L}]++~u', ' ', $key));
			$key = str_replace(' ', '_', strtolower($key));

			$value = trim($crawler->filter('td')->siblings()->text());

			$stats[$key] = (float) $value;
		}
		return $stats;
	}

}

Class UserDetails {
	public $last_online;
	public $gender;
	public $birthday;
	public $location;
	public $website;
	public $join_date;
	public $access_rank;
	public $anime_list_views = 0;
	public $manga_list_views = 0;
	public $forum_posts = 0;
	public $aim;
	public $comments = 0;
	public $msn;
	public $yahoo;
}