<?php
namespace Atarashii\APIBundle\Parser;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;
use Atarashii\APIBundle\Model\Anime;
use Atarashii\APIBundle\Model\Manga;

class Popular {

	public static function parse($contents, $type) {
		$crawler = new Crawler();
		$crawler->addHTMLContent($contents, 'UTF-8');

		//Filter into a set of tds from the source HTML table
		$mediaitems = $crawler->filter('#horiznav_nav')->nextAll()->filterXPath('./div/table/tr');

		foreach($mediaitems as $item) {
			$resultset[] = self::parseRecord($item, $type);
		}

		return $resultset;
	}

	private static function parseRecord($item, $type) {
		$crawler = new Crawler($item);

		//Initialize our object based on the record type we were passed.
		switch($type) {
			case 'anime':
				$media = new Anime();
				break;
			case 'manga':
				$media = new Manga();
				break;
		}

		//Pull out all the common parts
		$media->id = (int) str_replace('#area','',$crawler->filter('a')->attr('id'));
		$media->title = trim($crawler->filter('strong')->text());
		$media->image_url = str_replace('t.jpg','.jpg',$crawler->filter('img')->attr('src')); //Convert thumbnail to full size image by stripping the "t" in the filename
		$media->members_count = (int) trim(str_replace(',', '', str_replace('members', '', $crawler->filter('div.spaceit_pad span.lightLink')->text())));

		//Anime and manga have different details, so we grab an array of the list and then process based on the type
		$details = explode(', ', str_replace($crawler->filter('div.spaceit_pad span')->text(), '', $crawler->filter('div.spaceit_pad')->text()));

		switch($type) {
			case 'anime':
				$media->type = trim($details[0]);
				$media->episodes = (strstr($details[1], '?') ? null : (int) trim(str_replace('eps', '', $details[1])));
				$media->members_score = (float) trim(str_replace('scored', '', $details[2]));
				break;
			case 'manga':
				$media->volumes = (strstr($details[0], '?') ? null : (int) trim(str_replace('volumes', '', $details[0])));
				$media->members_score = (float) trim(str_replace('scored', '', $details[1]));
				break;
		}

		return $media;
	}
}