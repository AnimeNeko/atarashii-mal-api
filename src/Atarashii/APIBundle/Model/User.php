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

		$maincontent = iterator_to_array($crawler->filter('#horiznav_nav')->nextAll()->filterXPath('./div/table/tr/td'));

		$userdetails = $maincontent[0];
		$animestats = $maincontent[2];
		$mangastats = $maincontent[3];

		$this->details = $this->parseDetails($userdetails);
		$this->anime_stats = $this->parseStats($animestats);
		$this->manga_stats = $this->parseStats($mangastats);
	}

	private function parseDetails($content) {

		$details = new UserDetails();

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
					$value = 'http://' . $value;
					break;
			}

			if(property_exists($details, $key)) {
				$details->$key = $value;
			}

		}

		return $details;
	}

	private function parseStats($content) {
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