<?php
/**
* Atarashii MAL API.
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014-2015 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/
namespace Atarashii\APIBundle\Model;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Until;

class Anime
{
    /**
     * The ID of the Anime.
     *
     * @Type("integer")
     */
    private $id;

    /**
     * Title of the anime.
     *
     * @Type("string")
     */
    private $title;

    /**
     * Map of other titles for the anime.
     *
     * @Type("array<string, array<string>>")
     */
    private $otherTitles = array();

    /**
     * The global rank of the anime.
     *
     * @Type("integer")
     */
    private $rank;

    /**
     * Global rank of the anime based on popularity (number of people with the title on the list).
     *
     * @Type("integer")
     */
    private $popularityRank;

    /**
     * URL of tan image to the anime.
     *
     * @Type("string")
     */
    private $imageUrl;

    /**
     * Type of anime.
     *
     * Defined string from the type of anime. Value will be one of TV, Movie, OVA, ONA, Special, or Music.
     *
     * @Type("string")
     */
    private $type;

    /**
     * Total number of episodes of the anime.
     *
     * This value is the number of episodes of the anime, or null if unknown.
     *
     * @Type("integer")
     */
    private $episodes;

    /**
     * Airing status of the anime.
     *
     * Defines string of the status of the anime. Value will be one of finished airing, currently airing, or not yet aired.
     *
     * @Type("string")
     */
    private $status;

    /**
     * Beginning date from which this anime was/will be aired.
     *
     * API 1.0
     * This is the starting date of the anime, formatted for compatibility with the old Ruby API.
     * It is formatted as "D M d H:i:s O Y" or just a four-digit year. The time should be ignored.
     * The value is null if the date is unknown.
     * Example: "Thu Oct 07 22:28:07 -0700 2004" or "2004".
     *
     * @Type("string")
     * @Until("2.0")
     */
    private $startDate;

    /**
     * Airing end date for the anime.
     *
     * API 1.0
     * This is the starting date of the anime, formatted for compatibility with the old Ruby API.
     * It is formatted as "D M d H:i:s O Y" or just a four-digit year. The time should be ignored.
     * The value is null if the date is unknown.
     * Example: "Thu Oct 07 22:28:07 -0700 2004" or "2004".
     *
     * @Type("string")
     * @Until("2.0")
     */
    private $endDate;

    /**
     * Beginning date from which this anime was/will be aired.
     *
     * API 2.0+
     * This is the starting date of the anime, formatted as an ISO8601-compatible string.
     * The contents my be formatted as a year, year and month, or year, month and day.
     * The value is null if the date is unknown.
     * Example: "2004-10-07", "2004-10", or "2004".
     *
     * @SerializedName("start_date")
     * @Type("string")
     * @Since("2.0")
     */
    private $startDate2;

    /**
     * Airing end date for the anime.
     *
     * API 2.0+
     * This is the starting date of the anime, formatted as an ISO8601-compatible string.
     * The contents my be formatted as a year, year and month, or year, month and day.
     * The value is null if the date is unknown.
     * Example: "2004-10-07", "2004-10", or "2004".
     *
     * @SerializedName("end_date")
     * @Type("string")
     * @Since("2.0")
     */
    private $endDate2;

    /**
     * Rating of the anime.
     *
     * The rating is a freeform text field with no defined values.
     *
     * @Type("string")
     */
    private $classification;

    /**
     * Weighted score of the anime.
     *
     * The score is calculated based on the ratings given by members.
     *
     * @Type("double")
     */
    private $membersScore;

    /**
     * The number of members that have the anime on the list.
     *
     * @Type("integer")
     */
    private $membersCount;

    /**
     * The number of members that have the anime marked as a favorite.
     *
     * @Type("integer")
     */
    private $favoritedCount;

    /**
     * Description of the anime.
     *
     * An HTML-formatted string describing the anime
     *
     * @Type("string")
     */
    private $synopsis;

    /**
     * A list of producers for the anime.
     *
     * @Type("array<string>")
     * @Since("2.0")
     */
    private $producers = array();

    /**
     * A list of genres for the anime.
     *
     * @Type("array<string>")
     */
    private $genres = array();

    /**
     * A list of popular tags for the anime.
     *
     * @Type("array<string>")
     */
    private $tags = array();

    /**
     * A list of manga adaptations of this anime (or conversely, manga from which this anime is adapted).
     */
    private $mangaAdaptations = array();

    /**
     * A list of anime prequels of this anime.
     */
    private $prequels = array();

    /**
     * A list of anime sequels of this anime.
     */
    private $sequels = array();

    /**
     * A list of anime side stories of this anime.
     */
    private $sideStories = array();

    /**
     * Parent story of this anime.
     */
    private $parentStory;

    /**
     * A list of character anime of this anime.
     */
    private $characterAnime = array();

    /**
     * A list of spin-offs of this anime.
     */
    private $spinOffs = array();

    /**
     * A list of summaries of this anime.
     */
    private $summaries = array();

    /**
     * A list of alternative versions of this anime.
     */
    private $alternativeVersions = array();

    /**
     * A list of other related animes.
     *
     * @Since("2.0")
     */
    private $other = array();

    /**
     * Personal watched status of the anime.
     *
     * Defined string. Value will be one of watching, completed, on-hold, dropped, or plan to watch.
     *
     * @Type("string")
     */
    private $watchedStatus;

    /**
     * Number of episodes watched by the user.
     *
     * @Type("integer")
     */
    private $watchedEpisodes;

    /**
     * Score from 1 to 10 given to the title by the user.
     *
     * @Type("integer")
     */
    private $score;

    /**
     * ID of the anime as per the user's list.
     *
     * API 1.0
     * This used to correspond to a unique ID for the title on the user's list, but is no longer used.
     *
     * @Type("integer")
     * @Until("1.1")
     */
    private $listedAnimeId;

    /**
     * Tags assigned by the user.
     *
     * @Type("array<string>")
     * @Since("2.0")
     */
    private $personalTags = array();

    /**
     * The date the user started watching the show.
     *
     * @Type("DateTime<'Y-m-d'>")
     * @Since("2.0")
     */
    private $watchingStart;

    /**
     * The date the user finished watching the show.
     *
     * @Type("DateTime<'Y-m-d'>")
     * @Since("2.0")
     */
    private $watchingEnd;

    /**
     * The fansub group the user used, if any.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $fansubGroup;

    /**
     * Watching priority level for the anime title.
     *
     * Integer corresponding to the watching priority of the anime from 0 (low) to 2 (high).
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $priority;

    /**
     * Storage type for the series.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $storage;

    /**
     * The value for the storage chosen.
     *
     * This number may either be the number of discs (for DVDs, VHS, etc) or size in GB for HD types
     *
     * @Type("double")
     * @Since("2.0")
     */
    private $storageValue;

    /**
     * The number of episodes downloaded by the user.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $epsDownloaded;

    /**
     * Set if the user is rewatching the anime.
     *
     * @Type("boolean")
     * @Since("2.0")
     */
    private $rewatching;

    /**
     * The number of times the user has re-watched the title. (Does not include the first time.).
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $rewatchCount;

    /**
     * How much value the user thinks there is in rewatching the series.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $rewatchValue;

    /**
     * The user's personal comments on the title.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $personalComments;

    /**
     * Set the id property.
     *
     * @param int $id The id of series.
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the id property.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the title property.
     *
     * @param string $title The title of series.
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
     * Set the otherTitles property.
     *
     * @param array $otherTitles Other titles of series.
     */
    public function setOtherTitles($otherTitles)
    {
        $this->otherTitles = $otherTitles;
    }

    /**
     * Get the otherTitles property.
     *
     * @return array
     */
    public function getOtherTitles()
    {
        return $this->otherTitles;
    }

    /**
     * Set the rank property.
     *
     * @param int $rank The rank of series.
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
    }

    /**
     * Get the rank property.
     *
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set the popularityRank property.
     *
     * @param int $popularityRank The Popularity Rank of series.
     */
    public function setPopularityRank($popularityRank)
    {
        $this->popularityRank = $popularityRank;
    }

    /**
     * Get the popularityRank property.
     *
     * @return int
     */
    public function getPopularityRank()
    {
        return $this->popularityRank;
    }

    /**
     * Set the imageUrl property.
     *
     * @param string $imageUrl The Image url of series.
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * Get the imageUrl property.
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Set the type property.
     *
     * @param string|int $type The type of series.
     *                         Can be 1/TV, 2/OVA, 3/Movie, 4/Special, 5/ONA, or 6/Music. The default is "TV".
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
     * Get the type property.
     *
     * @return string (TV/OVA/Movie/Special/ONA/Music)
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the episodes property.
     *
     * @param int $episodes The number episodes of series.
     */
    public function setEpisodes($episodes)
    {
        $this->episodes = $episodes;
    }

    /**
     * Get the episodes property.
     *
     * @return int
     */
    public function getEpisodes()
    {
        return $this->episodes;
    }

    /**
     * Set the status property.
     *
     * @param int $status The broadcasting status of series.
     *                    Can be 1 (currently airing), 2 (finished airing) or 3 (not yet aired). The default is "2".
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
     * Get the status property.
     *
     * @return string (currently airing/finished airing/not yet aired)
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the startDate property with the literal value passed and call the setter for startDate2.
     *
     * This function is for compatibility with certain parts of API1 where the date format for the start and end dates
     * is not in a "parsed" format and instead is however is passed by MAL
     *
     * @param string   $literalDate The string that should be used as the start date.
     * @param DateTime $startDate   The start date of the series
     * @param string   $accuracy    To what level of accuracy this item is. May be "year", "month", or "day". Defaults to "day".
     */
    public function setLiteralStartDate($literalDate, $startDate, $accuracy = 'day')
    {
        $this->startDate = $literalDate;

        $this->setStartDate2($startDate, $accuracy);
    }

    /**
     * Set the startDate property and call the setter for startDate2.
     *
     * @param \DateTime $startDate The start date of the series
     * @param string    $accuracy  To what level of accuracy this item is. May be "year", "month", or "day". Defaults to "day".
     */
    public function setStartDate(\DateTime $startDate, $accuracy = 'day')
    {
        //For API 1.0 compatibility with the old Ruby API, dates that have an accuracy greater than a year
        //use a non-standard date format. For month-only accuracy, the day is always the 16th. The time returned
        //is always 12:00:00, but should be ignored as it's meaningless. For API 2 and greater, we use an ISO8601-compatible
        //date string with only the accuracy we know.

        switch ($accuracy) {
            case 'year':
                $this->startDate = $startDate->format('Y');
                break;
            case 'month':
                //For compatibility, API 1 always passes the 16th when the day is unknown.
                $this->startDate = $startDate->format('D M 16 12:00:00 O Y');
                break;
            case 'day':
            default:
                $this->startDate = $startDate->format('D M d 12:00:00 O Y');
        }

        $this->setStartDate2($startDate, $accuracy);
    }

    /**
     * Set the startDate2 property.
     *
     * @param \DateTime $startDate The start date of the series
     * @param string    $accuracy  To what level of accuracy this item is. May be "year", "month", or "day". Defaults to "day".
     */
    private function setStartDate2(\DateTime $startDate, $accuracy = 'day')
    {
        switch ($accuracy) {
            case 'year':
                $this->startDate2 = $startDate->format('Y');
                break;
            case 'month':
                $this->startDate2 = $startDate->format('Y-m');
                break;
            case 'day':
            default:
                $this->startDate2 = $startDate->format('Y-m-d');
        }
    }

    /**
     * Get the startDate property.
     *
     * @return string (ISO 8601)
     */
    public function getStartDate()
    {
        return $this->startDate2;
    }

    /**
     * Set the endDate property with the literal value passed and call the setter for endDate2.
     *
     * This function is for compatibility with certain parts of API1 where the date format for the start and end dates
     * is not in a "parsed" format and instead is however is passed by MAL
     *
     * @param string    $literalDate The string that should be used as the end date.
     * @param \DateTime $endDate     The end date of the series
     * @param string    $accuracy    To what level of accuracy this item is. May be "year", "month", or "day". Defaults to "day".
     */
    public function setLiteralEndDate($literalDate, \DateTime $endDate, $accuracy = 'day')
    {
        $this->endDate = $literalDate;

        $this->setendDate2($endDate, $accuracy);
    }

    /**
     * Set the endDate property and call the setter for endDate2.
     *
     * @param \DateTime $endDate  The end date of the series
     * @param string    $accuracy To what level of accuracy this item is. May be "year", "month", or "day". Defaults to "day".
     */
    public function setEndDate(\DateTime $endDate, $accuracy = 'day')
    {
        //For API 1.0 compatibility with the old Ruby API, dates that have an accuracy greater than a year
        //use a non-standard date format. For month-only accuracy, the day is always the 16th. The time returned
        //is always 12:00:00, but should be ignored as it's meaningless. For API 2 and greater, we use an ISO8601-compatible
        //date string with only the accuracy we know.

        switch ($accuracy) {
            case 'year':
                $this->endDate = $endDate->format('Y');
                break;
            case 'month':
                //For compatibility, API 1 always passes the 16th when the day is unknown.
                $this->endDate = $endDate->format('D M 16 12:00:00 O Y');
                break;
            case 'day':
            default:
                $this->endDate = $endDate->format('D M d 12:00:00 O Y');
        }

        $this->setEndDate2($endDate, $accuracy);
    }

    /**
     * Set the endDate2 property.
     *
     * @param \DateTime $endDate  The end date of the series
     * @param string    $accuracy To what level of accuracy this item is. May be "year", "month", or "day". Defaults to "day".
     */
    private function setEndDate2(\DateTime $endDate, $accuracy = 'day')
    {
        switch ($accuracy) {
            case 'year':
                $this->endDate2 = $endDate->format('Y');
                break;
            case 'month':
                $this->endDate2 = $endDate->format('Y-m');
                break;
            case 'day':
            default:
                $this->endDate2 = $endDate->format('Y-m-d');
        }
    }

    /**
     * Get the endDate property.
     *
     * @return string (ISO 8601)
     */
    public function getEndDate()
    {
        return $this->endDate2;
    }

    /**
     * Set the classification property.
     *
     * @param string $classification The MAL classification of series.
     */
    public function setClassification($classification)
    {
        $this->classification = $classification;
    }

    /**
     * Get the classification property.
     *
     * @return string
     */
    public function getClassification()
    {
        return $this->classification;
    }

    /**
     * Set the membersScore property.
     *
     * @param float $membersScore The score given by MAL members.
     */
    public function setMembersScore($membersScore)
    {
        $this->membersScore = (float) $membersScore;
    }

    /**
     * Get the membersScore property.
     *
     * @return float
     */
    public function getMembersScore()
    {
        return $this->membersScore;
    }

    /**
     * Set the membersCount property.
     *
     * @param int $membersCount The number of members that added this serie in their list.
     */
    public function setMembersCount($membersCount)
    {
        $this->membersCount = (int) $membersCount;
    }

    /**
     * Get the membersCount property.
     *
     * @return int
     */
    public function getMembersCount()
    {
        return $this->membersCount;
    }

    /**
     * Set the favoritedCount property.
     *
     * @param int $favoritedCount The number of members that added this serie in their favorite list.
     */
    public function setFavoritedCount($favoritedCount)
    {
        $this->favoritedCount = $favoritedCount;
    }

    /**
     * Get the favoritedCount property.
     *
     * @return int
     */
    public function getFavoritedCount()
    {
        return $this->favoritedCount;
    }

    /**
     * Set the synopsis property.
     *
     * @param string $synopsis The Text describing the anime.
     */
    public function setSynopsis($synopsis)
    {
        $this->synopsis = $synopsis;
    }

    /**
     * Get the synopsis property.
     *
     * @return string
     */
    public function getSynopsis()
    {
        return $this->synopsis;
    }

    /**
     * Set the producers property.
     *
     * @param string $producers The producers of series.
     */
    public function setProducers($producers)
    {
        $this->producers = $producers;
    }

    /**
     * Get the producers property.
     *
     * @return string
     */
    public function getProducers()
    {
        return $this->producers;
    }

    /**
     * Set the genres property.
     *
     * @param string $genres The genres of series.
     */
    public function setGenres($genres)
    {
        $this->genres = $genres;
    }

    /**
     * Get the genres property.
     *
     * @return string
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Set the tags property.
     *
     * @param string $tags The tags of series.
     */
    public function setTags($tags)
    {
        $this->tags[] = $tags;
    }

    /**
     * Get the tags property.
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set the mangaAdaptations property.
     *
     * @param string $mangaAdaptations The manga adaptations of series.
     */
    public function setMangaAdaptations($mangaAdaptations)
    {
        $this->mangaAdaptations[] = $mangaAdaptations;
    }

    /**
     * Get the mangaAdaptations property.
     *
     * @return array
     */
    public function getMangaAdaptations()
    {
        return $this->mangaAdaptations;
    }

    /**
     * Set the prequels property.
     *
     * @param string prequels The prequels of series.
     */
    public function setPrequels($prequels)
    {
        $this->prequels[] = $prequels;
    }

    /**
     * Get the prequels property.
     *
     * @return array
     */
    public function getPrequels()
    {
        return $this->prequels;
    }

    /**
     * Set the sequels property.
     *
     * @param string $sequels The sequels of series.
     */
    public function setSequels($sequels)
    {
        $this->sequels[] = $sequels;
    }

    /**
     * Get the sequels property.
     *
     * @return array
     */
    public function getSequels()
    {
        return $this->sequels;
    }

    /**
     * Set the sideStories property.
     *
     * @param string $sideStories The side stories of series.
     */
    public function setSideStories($sideStories)
    {
        $this->sideStories[] = $sideStories;
    }

    /**
     * Get the sideStories property.
     *
     * @return array
     */
    public function getSideStories()
    {
        return $this->sideStories;
    }

    /**
     * Set the parentStory property.
     *
     * @param string $parentStory The parent stories of series.
     */
    public function setParentStory($parentStory)
    {
        $this->parentStory = $parentStory;
    }

    /**
     * Get the parentStory property.
     *
     * @return string
     */
    public function getParentStory()
    {
        return $this->parentStory;
    }

    public function setCharacterAnime($characterAnime)
    {
        $this->characterAnime[] = $characterAnime;
    }

    /**
     * Get the characterAnime property.
     *
     * @return array
     */
    public function getCharacterAnime()
    {
        return $this->characterAnime;
    }

    /**
     * Set the spinOffs property.
     *
     * @param string $spinOffs The spin offs of series.
     */
    public function setSpinOffs($spinOffs)
    {
        $this->spinOffs[] = $spinOffs;
    }

    /**
     * Get the spinOffs property.
     *
     * @return array
     */
    public function getSpinOffs()
    {
        return $this->spinOffs;
    }

    /**
     * Set the summaries property.
     *
     * @param string $summaries The summaries of series.
     */
    public function setSummaries($summaries)
    {
        $this->summaries[] = $summaries;
    }

    /**
     * Get the summaries property.
     *
     * @return array
     */
    public function getSummaries()
    {
        return $this->summaries;
    }

    /**
     * Set the alternativeVersions property.
     *
     * @param string $alternativeVersions The alternative versions of series.
     */
    public function setAlternativeVersions($alternativeVersions)
    {
        $this->alternativeVersions[] = $alternativeVersions;
    }

    /**
     * Get the alternativeVersions property.
     *
     * @return array
     */
    public function getAlternativeVersions()
    {
        return $this->alternativeVersions;
    }

    /**
     * Set the other property.
     *
     * @param string $other The other animes of series.
     */
    public function setOther($other)
    {
        $this->other[] = $other;
    }

    /**
     * Get the other property.
     *
     * @return array
     */
    public function getOther()
    {
        return $this->other;
    }

    /**
     * Add a relation.
     *
     * @param array  $item An array containing details of the related item
     * @param string $type The type of relation, all lowercase with the underscore replacing spaces
     */
    public function addRelation($item, $type)
    {
        switch ($type) {
            case 'adaptation':
                $this->setMangaAdaptations($item);
                break;
            case 'prequel':
                $this->setPrequels($item);
                break;
            case 'sequel':
                $this->setSequels($item);
                break;
            case 'side_story':
                $this->setSideStories($item);
                break;
            case 'parent_story':
                $this->setParentStory($item);
                break;
            case 'character':
                $this->setCharacterAnime($item);
                break;
            case 'spin-off':
                $this->setSpinOffs($item);
                break;
            case 'summary':
                $this->setSummaries($item);
                break;
            case 'alternative_version':
                $this->setAlternativeVersions($item);
                break;
            case 'other':
                $this->setOther($item);
                break;
        }
    }

    /**
     * Set the watchedStatus property.
     *
     * @param string|int $status The input status value of an item.
     *                           Accepts either integers as defined by the MAL API module, or strings as defined by the Ruby API (mal-api.com).
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
     *                     Currently accepts either "string" or "int". Defaults to "string".
     *
     * @return string|int
     */
    public function getWatchedStatus($type = 'string')
    {
        if ($type == 'int') {
            switch ($this->watchedStatus) {
                case 'completed':
                    $status = 2;
                    break;
                case 'on-hold':
                    $status = 3;
                    break;
                case 'dropped':
                    $status = 4;
                    break;
                case 'plan to watch':
                    $status = 6;
                    break;
                case 'watching':
                default:
                    $status = 1;
            }

            return $status;
        } else {
            return $this->watchedStatus;
        }
    }

    /**
     * Set the watchedEpisodes property.
     *
     * @param string $watchedEpisodes The number of watched episodes.
     */
    public function setWatchedEpisodes($watchedEpisodes)
    {
        $this->watchedEpisodes = $watchedEpisodes;
    }

    /**
     * Get the watchedEpisodes property.
     *
     * @return int
     */
    public function getWatchedEpisodes()
    {
        return $this->watchedEpisodes;
    }

    /**
     * Set the score property.
     *
     * @param int $score The score of series.
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * Get the score property.
     *
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set the listedAnimeId property.
     *
     * @param int $listedAnimeId For internal use.
     */
    public function setListedAnimeId($listedAnimeId)
    {
        $this->listedAnimeId = $listedAnimeId;
    }

    /**
     * Get the listedAnimeId property.
     *
     * @return int
     */
    public function getListedAnimeId()
    {
        return $this->listedAnimeId;
    }

    public function getPersonalTags()
    {
        return $this->personalTags;
    }

    public function setPersonalTags($tags)
    {
        $this->personalTags = $tags;
    }

    public function getWatchingStart()
    {
        return $this->watchingStart;
    }

    public function setWatchingStart($date)
    {
        $this->watchingStart = $date;
    }

    public function getWatchingEnd()
    {
        return $this->watchingEnd;
    }

    public function setWatchingEnd($date)
    {
        $this->watchingEnd = $date;
    }

    public function getFansubGroup()
    {
        return $this->fansubGroup;
    }

    public function setFansubGroup($group)
    {
        $this->fansubGroup = $group;
    }

    /**
     * Get the current watching priority for the anime title.
     *
     * @param string $type What type you want to get back.
     *                     Currently accepts either "string" or "int". Defaults to "int".
     *
     * @return string|int
     */
    public function getPriority($type = 'int')
    {
        if ($type == 'string') {
            switch ($this->priority) {
                case 1:
                    return 'Medium';
                    break;
                case 2:
                    return 'High';
                    break;
                default:
                    return 'Low';
                    break;
            }
        } else {
            return $this->priority;
        }
    }

    public function setPriority($priority)
    {
        $priority = (int) $priority;

        //Only allow 0-2. If outside the range, force to 0.
        if ($priority > 2 || $priority < 0) {
            $priority = 0;
        }

        $this->priority = $priority;
    }

    /**
     * Get the current value for the storage type that the anime is on.
     *
     * @param string $type What type you want to get back.
     *                     Currently accepts either "string" or "int". Defaults to "int".
     *
     * @return string|int
     */
    public function getStorage($type = 'int')
    {
        if ($type == 'string') {
            switch ($this->storage) {
                case 1:
                    return 'Hard Drive';
                    break;
                case 2:
                    return 'DVD / CD';
                    break;
                case 3:
                    return 'None';
                    break;
                case 4:
                    return 'Retail DVD';
                    break;
                case 5:
                    return 'VHS';
                    break;
                case 6:
                    return 'External HD';
                    break;
                case 7:
                    return 'NAS';
                    break;
                default:
                    return;
                    break;
            }
        } else {
            return $this->storage;
        }
    }

    public function setStorage($storage)
    {
        $storage = (int) $storage;

        if ($storage > 0 || $storage <= 7) {
            $this->storage = $storage;
        }
    }

    public function getStorageValue()
    {
        return $this->storageValue;
    }

    public function setStorageValue($value)
    {
        $this->storageValue = (float) $value;
    }

    public function getEpsDownloaded()
    {
        return $this->epsDownloaded;
    }

    public function setEpsDownloaded($downloaded)
    {
        $this->epsDownloaded = (int) $downloaded;
    }

    public function getRewatching()
    {
        return $this->rewatching;
    }

    public function setRewatching($rewatching)
    {
        $this->rewatching = (bool) $rewatching;
    }

    public function getRewatchCount()
    {
        return $this->rewatchCount;
    }

    public function setRewatchCount($count)
    {
        $this->rewatchCount = (int) $count;
    }

    /**
     * Get the current watching priority for the anime title.
     *
     * @param string $type What type you want to get back.
     *                     Currently accepts either "string" or "int". Defaults to "int".
     *
     * @return string|int
     */
    public function getRewatchValue($type = 'int')
    {
        if ($type == 'string') {
            switch ($this->rewatchValue) {
                case 1:
                    return 'Very Low';
                    break;
                case 2:
                    return 'Low';
                    break;
                case 3:
                    return 'Medium';
                    break;
                case 4:
                    return 'High';
                    break;
                case 5:
                    return 'Very High';
                    break;
                default:
                    return;
                    break;
            }
        } else {
            return $this->rewatchValue;
        }
    }

    public function setRewatchValue($value)
    {
        $this->rewatchValue = (int) $value;
    }

    public function getPersonalComments()
    {
        return $this->personalComments;
    }

    public function setPersonalComments($comments)
    {
        $this->personalComments = $comments;
    }

    /**
     * Return a formatted XML document for updating MAL.
     *
     * @param array $update_items An array listing the items that should be added to the XML
     *
     * @return string An XML document of anime values as defined at http://myanimelist.net/modules.php?go=api#animevalues
     */
    public function MALApiXml($update_items)
    {
        //For now, just add in the parameters we will use. The MAL API will handle missing items just fine.
        $xml = new \SimpleXMLElement('<entry/>');

        if (in_array('episodes', $update_items)) {
            $xml->addChild('episode', $this->getWatchedEpisodes());
        }

        if (in_array('status', $update_items)) {
            $xml->addChild('status', $this->getWatchedStatus('int')); //Use int for the MAL API to eliminate problems with strings.
        }

        if (in_array('score', $update_items)) {
            $xml->addChild('score', $this->getScore());
        }

        if (in_array('downloaded', $update_items)) {
            $xml->addChild('downloaded_episodes', $this->getEpsDownloaded());
        }

        if (in_array('storage', $update_items)) {
            $xml->addChild('storage_type', $this->getStorage('int'));
        }

        if (in_array('storageAmt', $update_items)) {
            $xml->addChild('storage_value', $this->getStorageValue());
        }

        if (in_array('rewatchCount', $update_items)) {
            $xml->addChild('times_rewatched', $this->getRewatchCount());
        }

        if (in_array('rewatchValue', $update_items)) {
            $xml->addChild('rewatch_value', $this->getRewatchValue('int'));
        }

        if (in_array('start', $update_items)) {
            // Date must be MMDDYYYY.
            $xml->addChild('date_start', $this->getWatchingStart()->format('mdY'));
        }

        if (in_array('end', $update_items)) {
            // Date must be MMDDYYY.
            $xml->addChild('date_finish', $this->getWatchingEnd()->format('mdY'));
        }

        if (in_array('priority', $update_items)) {
            $xml->addChild('priority', $this->getPriority('int'));
        }

        if (in_array('comments', $update_items)) {
            $xml->addChild('comments', $this->getPersonalComments());
        }

        if (in_array('fansubber', $update_items)) {
            $xml->addChild('fansub_group', $this->getFansubGroup());
        }

        if (in_array('tags', $update_items)) {
            $xml->addChild('tags', $this->getPersonalTags());
        }

        return $xml->asXML();
    }
}
