<?php
/**
* Atarashii MAL API
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/

namespace Atarashii\APIBundle\Model;

class Anime
{
    private $id; //The anime ID.
    private $title; //The anime title.
    private $otherTitles = array(); //A hash/dictionary containing other titles this anime has.
    private $rank; //Global rank of this anime. Not available in /animelist requests.
    private $popularityRank; //Rank of this anime based on its popularity, i.e. number of users that have added this anime. Not available in /animelist requests.
    private $imageUrl; //URL to an image for this anime.
    private $type; //Type of anime. Possible values: TV, Movie, OVA, ONA, Special, Music.
    private $episodes; //Number of episodes. null is returned if the number of episodes is unknown.
    private $status; //Airing status of this anime. Possible values: finished airing, currently airing, not yet aired.
    private $startDate; //Beginning date from which this anime was/will be aired.
    private $endDate; //Ending air date of this anime.
    private $classification; //Classification or rating of this anime. This is a freeform text field, with possible values like: R - 17+ (violence & profanity), PG - Children. Not available in /animelist requests.
    private $membersScore; //Weighted score members of MyAnimeList have given to this anime. Not available in /animelist requests.
    private $membersCount; //Number of members who have this anime on their list. Not available in /animelist requests.
    private $favoritedCount; //Number of members who have this anime marked as one of their favorites. Not available in /animelist requests.
    private $synopsis; //Text describing the anime. Not available in /animelist requests.
    private $genres = array(); //A list of genres for this anime, e.g. ["Action", "Comedy", "Shounen"]. Not available in /animelist requests.
    private $tags = array(); //A list of popular tags for this anime, e.g. ["supernatural", "comedy"]. Not available in /animelist requests.
    private $mangaAdaptations = array(); //A list of manga adaptations of this anime (or conversely, manga from which this anime is adapted). Not available in /animelist requests.
    private $prequels = array(); //A list of anime prequels of this anime. Not available in /animelist requests.
    private $sequels = array(); //A list of anime sequels of this anime. Not available in /animelist requests.
    private $sideStories = array(); //A list of anime side stories of this anime. Not available in /animelist requests.
    private $parentStory; //Parent story of this anime. Not available in /animelist requests.
    private $characterAnime = array(); //A list of character anime of this anime. Not available in /animelist requests.
    private $spinOffs = array(); //A list of spin-offs of this anime. Not available in /animelist requests.
    private $summaries = array(); //A list of summaries of this anime. Not available in /animelist requests.
    private $alternativeVersions = array(); //A list of alternative versions of this anime. Not available in /animelist requests.
    private $watchedStatus; //User's watched status of the anime. This is a string that is one of: watching, completed, on-hold, dropped, plan to watch.
    private $watchedEpisodes; //Number of episodes already watched by the user.
    private $score; //User's score for the anime, from 1 to 10.
    private $listedAnimeId; //For internal use. This is not listed as a public part of the returned list and it seems to only be used internally in the Ruby API.

    /**
     * Set the id property
     *
     * @param int $id The id of series.
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the id property
     *
     * @return int
     */
    public function getId()
    {
       return $this->id;
    }

    /**
     * Set the title property
     *
     * @param string $title The title of series.
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get the title property.
     *
     * @return string
     */
    public function getTitle()
    {
       return $this->title;
    }

    /**
     * Set the other_titles property
     *
     * @param array $other_titles Other titles of series.
     *
     * @return void
     */
    public function setOtherTitles($other_titles)
    {
        $this->otherTitles = $other_titles;
    }

    /**
     * Get the other_titles property
     *
     * @return array
     */
    public function getOtherTitles()
    {
       return $this->otherTitles;
    }

    /**
     * Set the rank property
     *
     * @param int $rank The rank of series.
     *
     * @return void
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
    }

    /**
     * Get the rank property
     *
     * @return int
     */
    public function getRank()
    {
       return $this->rank;
    }

    /**
     * Set the popularity_rank property
     *
     * @param int $popularity_rank The Popularity Rank of series.
     *
     * @return void
     */
    public function setPopularityRank($popularity_rank)
    {
        $this->popularityRank = $popularity_rank;
    }

    /**
     * Get the popularity_rank property
     *
     * @return int
     */
    public function getPopularityRank()
    {
       return $this->popularityRank;
    }

    /**
     * Set the image_url property
     *
     * @param string $image_url The Image url of series.
     *
     * @return void
     */
    public function setImageUrl($image_url)
    {
        $this->imageUrl = $image_url;
    }

    /**
     * Get the image_url property
     *
     * @return string
     */
    public function getImageUrl()
    {
       return $this->imageUrl;
    }

    /**
     * Set the type property
     *
     * @param string|int $type The type of series.
     *     Can be 1/TV, 2/OVA, 3/Movie, 4/Special, 5/ONA, or 6/Music. The default is "TV".
     *
     * @return void
     */
    public function setType($type)
    {
        switch ($type) {
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
     * Get the type property
     *
     * @return string (TV/OVA/Movie/Special/ONA/Music)
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the episodes property
     *
     * @param int $episodes The number episodes of series.
     *
     * @return void
     */
    public function setEpisodes($episodes)
    {
        $this->episodes = $episodes;
    }

    /**
     * Get the episodes property
     *
     * @return int
     */
    public function getEpisodes()
    {
       return $this->episodes;
    }

    /**
     * Set the status property
     *
     * @param int $status The broadcasting status of series.
     *     Can be 1 (currently airing), 2 (finished airing) or 3 (not yet aired). The default is "2".
     *
     * @return void
     */
    public function setStatus($status)
    {
        switch ($status) {
            case 1:
            case 'currently airing':
                $this->status = 'currently airing';
                break;
            case 2:
            case 'finished airing':
                $this->status = 'finished airing';
                break;
            case 3:
            case 'not yet aired':
                $this->status = 'not yet aired';
                break;
            default:
                $this->status = 'finished airing';
                break;
        }
    }

    /**
     * Get the status property
     *
     * @return string (currently airing/finished airing/not yet aired)
     */
    public function getstatus()
    {
       return $this->status;
    }

    /**
     * Set the start_date property
     *
     * @param string $start_date The ISO 8601 start date of series.
     *
     * @return void
     */
    public function setStartDate($start_date)
    {
        $this->startDate = $start_date;
    }

    /**
     * Get the start_date property
     *
     * @return string (ISO 8601)
     */
    public function getStartDate()
    {
       return $this->startDate;
    }

    /**
     * Set the end_date property
     *
     * @param string $end_date The ISO 8601 end date of series.
     *
     * @return void
     */
    public function setEndDate($end_date)
    {
        $this->endDate = $end_date;
    }

    /**
     * Get the end_date property
     *
     * @return string (ISO 8601)
     */
    public function getEndDate()
    {
       return $this->endDate;
    }

    /**
     * Set the classification property
     *
     * @param string $classification The MAL classification of series.
     *
     * @return void
     */
    public function setClassification($classification)
    {
        $this->classification = $classification;
    }

    /**
     * Get the classification property
     *
     * @return string
     */
    public function getClassification()
    {
       return $this->classification;
    }

    /**
     * Set the members_score property
     *
     * @param float $members_score The score given by MAL members.
     *
     * @return void
     */
    public function setMembersScore($members_score)
    {
        $this->membersScore = $members_score;
    }

    /**
     * Get the members_score property
     *
     * @return float
     */
    public function getMembersScore()
    {
        return $this->membersScore;
    }

    /**
     * Set the members_count property
     *
     * @param int $members_count The number of members that added this serie in their list.
     *
     * @return void
     */
    public function setMembersCount($members_count)
    {
        $this->membersCount = $members_count;
    }

    /**
     * Get the members_count property
     *
     * @return int
     */
    public function getMembersCount()
    {
        return $this->membersCount;
    }

    /**
     * Set the favorited_count property
     *
     * @param int $favorited_count The number of members that added this serie in their favorite list.
     *
     * @return void
     */
    public function setFavoritedCount($favorited_count)
    {
        $this->favoritedCount = $favorited_count;
    }

    /**
     * Get the favorited_count property
     *
     * @return int
     */
    public function getFavoritedCount()
    {
        return $this->favoritedCount;
    }

    /**
     * Set the synopsis property
     *
     * @param string $synopsis The Text describing the anime.
     *
     * @return void
     */
    public function setSynopsis($synopsis)
    {
        $this->synopsis = $synopsis;
    }

    /**
     * Get the synopsis property
     *
     * @return string
     */
    public function getSynopsis()
    {
        return $this->synopsis;
    }

    /**
     * Set the genres property
     *
     * @param string $genres The genres of series.
     *
     * @return void
     */
    public function setGenres($genres)
    {
        $this->genres = $genres;
    }

    /**
     * Get the genres property
     *
     * @return string
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Set the tags property
     *
     * @param string $tags The tags of series.
     *
     * @return void
     */
    public function setTags($tags)
    {
        $this->tags[] = $tags;
    }

    /**
     * Get the tags property
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set the manga_adaptations property
     *
     * @param string $manga_adaptations The manga adaptations of series.
     *
     * @return void
     */
    public function setMangaAdaptations($manga_adaptations)
    {
        $this->mangaAdaptations[] = $manga_adaptations;
    }

    /**
     * Get the manga_adaptations property
     *
     * @return array
     */
    public function getMangaAdaptations()
    {
        return $this->mangaAdaptations;
    }

    /**
     * Set the prequels property
     *
     * @param string prequels The prequels of series.
     *
     * @return void
     */
    public function setPrequels($prequels)
    {
        $this->prequels[] = $prequels;
    }

    /**
     * Get the prequels property
     *
     * @return array
     */
    public function getPrequels()
    {
        return $this->prequels;
    }

    /**
     * Set the sequels property
     *
     * @param string $sequels The sequels of series.
     *
     * @return void
     */
    public function setSequels($sequels)
    {
        $this->sequels[] = $sequels;
    }

    /**
     * Get the sequels property
     *
     * @return array
     */
    public function getSequels()
    {
        return $this->sequels;
    }

    /**
     * Set the side_stories property
     *
     * @param string $side_stories The side stories of series.
     *
     * @return void
     */
    public function setSideStories($side_stories)
    {
        $this->sideStories[] = $side_stories;
    }

    /**
     * Get the side_stories property
     *
     * @return array
     */
    public function getSideStories()
    {
        return $this->sideStories;
    }

    /**
     * Set the parent_story property
     *
     * @param string $parent_story The parent stories of series.
     *
     * @return void
     */
    public function setParentStory($parent_story)
    {
        $this->parentStory = $parent_story;
    }

    /**
     * Get the parent_story property
     *
     * @return string
     */
    public function getParentStory()
    {
        return $this->parentStory;
    }

    public function setCharacterAnime($character_anime)
    {
        $this->characterAnime[] = $character_anime;
    }

    /**
     * Get the character_anime property
     *
     * @return array
     */
    public function getCharacterAnime()
    {
        return $this->characterAnime;
    }

    /**
     * Set the spin_offs property
     *
     * @param string $spin_offs The spin offs of series.
     *
     * @return void
     */
    public function setSpinOffs($spin_offs)
    {
        $this->spinOffs[] = $spin_offs;
    }

    /**
     * Get the spin_offs property
     *
     * @return array
     */
    public function getSpinOffs()
    {
        return $this->spinOffs;
    }

    /**
     * Set the summaries property
     *
     * @param string $summaries The summaries of series.
     *
     * @return void
     */
    public function setSummaries($summaries)
    {
        $this->summaries[] = $summaries;
    }

    /**
     * Get the summaries property
     *
     * @return array
     */
    public function getSummaries()
    {
        return $this->summaries;
    }

    /**
     * Set the alternative_versions property
     *
     * @param string $alternative_versions The alternative versions of series.
     *
     * @return void
     */
    public function setAlternativeVersions($alternative_versions)
    {
        $this->alternativeVersions[] = $alternative_versions;
    }

    /**
     * Get the alternative_versions property
     *
     * @return array
     */
    public function getAlternativeVersions()
    {
        return $this->alternativeVersions;
    }

    /**
     * Set the watched_status property
     *
     * @param string|int $status The input status value of an item.
     *     Accepts either integers as defined by the MAL API module, or strings as defined by the Ruby API (mal-api.com).
     *
     * @return void
     */
    public function setWatchedStatus($status)
    {
        switch ($status) {
            case 1:
            case 'watching':
                $this->watchedStatus = 'watching';
                break;
            case 2:
            case 'completed':
                $this->watchedStatus = 'completed';
                break;
            case 3:
            case 'on-hold':
            case 'onhold':
                $this->watchedStatus = 'on-hold';
                break;
            case 4:
            case 'dropped':
                $this->watchedStatus = 'dropped';
                break;
            case 6:
            case 'plan to watch':
            case 'plantowatch':
                $this->watchedStatus = 'plan to watch';
                break;
            default:
                $this->watchedStatus = 'watching';
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
    public function getWatchedStatus($type = 'string')
    {
        if ($type == 'int') {
            switch ($this->watchedStatus) {
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
            return $this->watchedStatus;
        }

    }

    /**
     * Set the watched_episodes property
     *
     * @param string $watched_episodes The number of watched episodes.
     *
     * @return void
     */
    public function setWatchedEpisodes($watched_episodes)
    {
        $this->watchedEpisodes = $watched_episodes;
    }

    /**
     * Get the watched_episodes property
     *
     * @return int
     */
    public function getWatchedEpisodes()
    {
        return $this->watchedEpisodes;
    }

    /**
     * Set the score property
     *
     * @param int $score The score of series.
     *
     * @return void
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * Get the score property
     *
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set the listed_anime_id property
     *
     * @param int $listed_anime_id For internal use.
     *
     * @return void
     */
    public function setListedAnimeId($listed_anime_id)
    {
        $this->listedAnimeId = $listed_anime_id;
    }

    /**
     * Get the listed_anime_id property
     *
     * @return int
     */
    public function getListedAnimeId()
    {
        return $this->listedAnimeId;
    }

    /**
     * Return a formatted XML document for updating MAL
     *
     * @return string An XML document of anime values as defined at http://myanimelist.net/modules.php?go=api#animevalues
     */
    public function MALApiXml()
    {
        //For now, just add in the parameters we will use. The MAL API will handle missing items just fine.
        $xml = new \SimpleXMLElement('<entry/>');

        $xml->addChild('episode', $this->getWatchedEpisodes());
        $xml->addChild('status', $this->getWatchedStatus('int')); //Use int for the MAL API to eliminate problems with strings.
        $xml->addChild('score', $this->getScore());

        return $xml->asXML();
    }

}
