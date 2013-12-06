<?php
namespace Atarashii\APIBundle\Model;

class Anime {
	public $id; //The anime ID.
	public $title; //The anime title.
	public $other_titles = array(); //A hash/dictionary containing other titles this anime has.
	public $rank; //Global rank of this anime. Not available in /animelist requests.
	public $popularity_rank; //Rank of this anime based on its popularity, i.e. number of users that have added this anime. Not available in /animelist requests.
	public $image_url; //URL to an image for this anime.
	public $type; //Type of anime. Possible values: TV, Movie, OVA, ONA, Special, Music.
	public $episodes; //Number of episodes. null is returned if the number of episodes is unknown.
	public $status; //Airing status of this anime. Possible values: finished airing, currently airing, not yet aired.
	public $start_date; //Beginning date from which this anime was/will be aired.
	public $end_date; //Ending air date of this anime.
	public $classification; //Classification or rating of this anime. This is a freeform text field, with possible values like: R - 17+ (violence & profanity), PG - Children. Not available in /animelist requests.
	public $members_score; //Weighted score members of MyAnimeList have given to this anime. Not available in /animelist requests.
	public $members_count; //Number of members who have this anime on their list. Not available in /animelist requests.
	public $favorited_count; //Number of members who have this anime marked as one of their favorites. Not available in /animelist requests.
	public $synopsis; //Text describing the anime. Not available in /animelist requests.
	public $genres = array(); //A list of genres for this anime, e.g. ["Action", "Comedy", "Shounen"]. Not available in /animelist requests.
	public $tags = array(); //A list of popular tags for this anime, e.g. ["supernatural", "comedy"]. Not available in /animelist requests.
	public $manga_adaptations = array(); //A list of manga adaptations of this anime (or conversely, manga from which this anime is adapted). Not available in /animelist requests.
	public $prequels = array(); //A list of anime prequels of this anime. Not available in /animelist requests.
	public $sequels = array(); //A list of anime sequels of this anime. Not available in /animelist requests.
	public $side_stories = array(); //A list of anime side stories of this anime. Not available in /animelist requests.
	public $parent_story; //Parent story of this anime. Not available in /animelist requests.
	public $character_anime = array(); //A list of character anime of this anime. Not available in /animelist requests.
	public $spin_offs = array(); //A list of spin-offs of this anime. Not available in /animelist requests.
	public $summaries = array(); //A list of summaries of this anime. Not available in /animelist requests.
	public $alternative_versions = array(); //A list of alternative versions of this anime. Not available in /animelist requests.
	public $watched_status; //User's watched status of the anime. This is a string that is one of: watching, completed, on-hold, dropped, plan to watch.
	public $watched_episodes; //Number of episodes already watched by the user.
	public $score; //User's score for the anime, from 1 to 10.
	public $listed_anime_id; //For internal use. This is not listed as a public part of the returned list and it seems to only be used internally in the Ruby API.

	public static function parseAnimeType($typeid) {
		switch($typeid) {
			case 1:
				return 'TV';
				break;
			case 2:
				return 'OVA';
				break;
			case 3:
				return 'Movie';
				break;
			case 4:
				return 'Special';
				break;
			case 5:
				return 'ONA';
				break;
			case 6:
				return 'Music';
				break;
			default:
				return 'TV';
				break;
		}
	}

	public static function parseWatchedStatus($statusid) {
		switch($statusid) {
			case 1:
				return 'watching';
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
				return 'plan to watch';
				break;
			default:
				return 'watching';
				break;
		}
	}

	public static function getWatchedStatus($status) {
		switch($status) {
			case 'watching':
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
			case 'plantowatch':
				return '6';
				break;
			default:
				return '1';
				break;
		}
	}

	public static function parseAnimeStatus($statusid) {
		switch($statusid) {
			case 1:
				return 'currently airing';
				break;
			case 2:
				return 'finished airing';
				break;
			case 3:
				return 'not yet aired';
				break;
			default:
				return 'finished airing';
				break;
		}
	}

	public static function setxmlAnime($episode,$status,$score) {
		$requestbody = '<?xml version="1.0" encoding="UTF-8"?><entry>';
		$requestbody = $requestbody.'<episode>'.$episode.'</episode>';
		$requestbody = $requestbody.'<status>'.$status.'</status>';
		$requestbody = $requestbody.'<score>'.$score.'</score>';
		$requestbody = $requestbody.'<downloaded_episodes></downloaded_episodes>';
		$requestbody = $requestbody.'<storage_type></storage_type>';
		$requestbody = $requestbody.'<storage_value></storage_value>';
		$requestbody = $requestbody.'<times_rewatched></times_rewatched>';
		$requestbody = $requestbody.'<rewatch_value></rewatch_value>';
		$requestbody = $requestbody.'<date_start></date_start>';
		$requestbody = $requestbody.'<date_finish></date_finish>';
		$requestbody = $requestbody.'<priority></priority>';
		$requestbody = $requestbody.'<enable_discussion></enable_discussion>';
		$requestbody = $requestbody.'<enable_rewatching></enable_rewatching>';
		$requestbody = $requestbody.'<comments></comments>';
		$requestbody = $requestbody.'<fansub_group></fansub_group>';
		$requestbody = $requestbody.'<tags></tags></entry>';
		return $requestbody;
	}

}