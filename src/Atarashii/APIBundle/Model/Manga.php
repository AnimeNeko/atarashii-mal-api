<?php
namespace Atarashii\APIBundle\Model;

class Manga {
	public $id; //The manga ID.
	public $title; //The manga title.
	public $other_titles = array(); //A hash/dictionary containing other titles this manga has.
	public $rank; //Global rank of this manga. Not available in /mangalist requests.
	public $popularity_rank; //Rank of this manga based on its popularity, i.e. number of users that have added this manga. Not available in /mangalist requests.
	public $image_url; //URL to an image for this manga.
	public $type; //Type of manga. Possible values: Manga, Novel, One Shot, Doujin, Manwha, Manhua, OEL ("OEL manga" refers to "Original English-Language manga").
	public $chapters; //Number of chapters. null is returned if the number of chapters is unknown.
	public $volumes; //Number of volumes. null is returned if the number of volumes is unknown.
	public $status; //Publishing status of this manga. Possible values: finished, publishing, not yet published.
	public $members_score; //Weighted score members of MyAnimeList have given to this manga. Not available in /mangalist requests.
	public $members_count; //Number of members who have this manga on their list. Not available in /mangalist requests.
	public $favorited_count; //Number of members who have this manga marked as one of their favorites. Not available in /mangalist requests.
	public $synopsis; //Text describing the manga. Not available in /mangalist requests.
	public $genres = array(); //A list of genres for this manga, e.g. ["Comedy", "Slice of Life"]. Not available in /mangalist requests.
	public $tags = array(); //A list of popular tags for this manga, e.g. ["comedy", "slice of life"]. Not available in /mangalist requests.
	public $anime_adaptations = array(); //A list of anime adaptations of this manga (or conversely, anime from which this manga is adapted). Not available in /mangalist requests.
	public $related_manga = array(); //A list of related manga.
	public $alternative_versions = array(); //A list of alternative versions of this manga.
	public $read_status; //User's read status of the anime. This is a string that is one of: reading, completed, on-hold, dropped, plan to read.
	public $chapters_read; //Number of chapters already read by the user.
	public $volumes_read; //Number of volumes already read by the user.
	public $score; //User's score for the manga, from 1 to 10.
	public $listed_manga_id; //For internal use. This is not listed as a public part of the returned list and it seems to only be used internally in the Ruby API.

	public static function parseMangaType($typeid) {
		switch($typeid) {
			case 1:
				return 'Manga';
				break;
			case 2:
				return 'Novel';
				break;
			case 3:
				return 'One Shot';
				break;
			case 4:
				return 'Doujin';
				break;
			case 5:
				return 'Manwha'; //Korean comics
				break;
			case 6:
				return 'Manhua'; //Chinese comics
				break;
			case 7:
				return 'OEL'; //Original English Language Manga
				break;
			default:
				return 'Manga';
				break;
		}
	}

	public static function parseReadStatus($statusid) {
		switch($statusid) {
			case 1:
				return 'reading';
				break;
			case 2:
				return 'completed';
				break;
			case 3:
				return 'on-hold';
				break;
			case 4:
				return 'dropped';
				break;
			case 6:
				return 'plan to read';
				break;
			default:
				return 'reading';
				break;
		}
	}

	public static function parseMangaStatus($statusid) {
		switch($statusid) {
			case 1:
				return 'publishing';
				break;
			case 2:
				return 'finished';
				break;
			case 3:
				return 'not yet published';
				break;
			default:
				return 'finished';
				break;
		}
	}

	public static function getReadStatus($status) {
		switch($status) {
			case 'reading':
				return '1';
				break;
			case 'completed':
				return '2';
				break;
			case 'onhold':
				return '3';
				break;
			case 'dropped':
				return '4';
				break;
			case 'plantoread':
				return '6';
				break;
			default:
				return '1';
				break;
		}
	}

	public function MALApiXml($manga) {
		//For now, just add in the parameters we will use. The MAL API will handle missing items just fine.
		$xml = new \SimpleXMLElement('<entry/>');

		$xml->addChild('chapter', $manga->chapters_read);
		$xml->addChild('volume', $manga->volumes_read);
		$xml->addChild('status', $manga->read_status);
		$xml->addChild('score', $manga->score);

		return $xml->asXML();
	}

}