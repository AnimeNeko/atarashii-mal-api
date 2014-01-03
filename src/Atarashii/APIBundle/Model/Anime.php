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

	/**
	 * Set the type property
	 *
	 * @param string|int $type The type of series.
	 *     Can be 1/TV, 2/OVA, 3/Movie, 4/Special, 5/ONA, or 6/Music. The default is "TV".
	 *
	 * @return void
	 */
	public function setType($type) {
		switch($type) {
			case 1:
			case 'TV':
				$this->type = 'TV';
				break;
			case 2:
			case 'OVA':
				$this->type = 'OVA';
				break;
			case 3:
			case 'Movie':
				$this->type = 'Movie';
				break;
			case 4:
			case 'Special':
				$this->type = 'Special';
				break;
			case 5:
			case 'ONA':
				$this->type = 'ONA';
				break;
			case 6:
			case 'Music':
				$this->type = 'Music';
				break;
			default:
				$this->type = 'TV';
				break;
		}
	}

	/**
	 * Set the watched_status property
	 *
	 * @param string|int $status The input status value of an item.
	 *     Accepts either integers as defined by the MAL API module, or strings as defined by the Ruby API (mal-api.com).
	 *
	 * @return void
	 */
	public function setWatchedStatus($status) {
		switch($status) {
			case 1:
			case 'watching':
				$this->watched_status = 'watching';
				break;
			case 2:
			case 'completed':
				$this->watched_status = 'completed';
				break;
			case 3:
			case 'on-hold':
			case 'onhold':
				$this->watched_status = 'on-hold';
				break;
			case 4:
			case 'dropped':
				$this->watched_status = 'dropped';
				break;
			case 6:
			case 'plan to watch':
			case 'plantowatch':
				$this->watched_status = 'plan to watch';
				break;
			default:
				$this->watched_status = 'watching';
				break;
		}
	}

	/**
	 * Get the current value of the watched status.
	 *
	 * @param string $type What type you want to get back.
	 *     Currently accepts either "string" or "int". Defaults to "string".
	 *
	 * @return string|int
	 */
	public function getWatchedStatus($type = 'string') {

		if($type == 'int') {
			switch($this->watched_status) {
				case 'watching':
					return 1;
					break;
				case 'completed':
					return 2;
					break;
				case 'on-hold':
					return 3;
					break;
				case 'dropped':
					return 4;
					break;
				case 'plan to watch':
					return 6;
					break;
				default:
					return 1;
					break;
			}
		} else {
			return $this->watched_status;
		}

	}

	/**
	 * Set the status property
	 *
	 * @param int $status The broadcasting status of series.
	 *     Can be 1 (currently airing), 2 (finished airing) or 3 (not yet aired). The default is "2".
	 *
	 * @return void
	 */
	public function setStatus($status) {
		switch($status) {
			case 1:
				$this->status = 'currently airing';
				break;
			case 2:
				$this->status = 'finished airing';
				break;
			case 3:
				$this->status = 'not yet aired';
				break;
			default:
				$this->status = 'finished airing';
				break;
		}
	}

	/**
	 * Return a formatted XML document for updating MAL
	 *
	 * @return string An XML document of anime values as defined at http://myanimelist.net/modules.php?go=api#animevalues
	 */
	public function MALApiXml() {
		//For now, just add in the parameters we will use. The MAL API will handle missing items just fine.
		$xml = new \SimpleXMLElement('<entry/>');

		$xml->addChild('episode', $this->watched_episodes);
		$xml->addChild('status', $this->getWatchedStatus('int')); //Use int for the MAL API to eliminate problems with strings.
		$xml->addChild('score', $this->score);

		return $xml->asXML();
	}

}