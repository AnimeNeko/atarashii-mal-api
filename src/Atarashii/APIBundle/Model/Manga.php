<?php
/**
* Atarashii MAL API.
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014-2016 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/

namespace Atarashii\APIBundle\Model;

use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Until;
use Atarashii\APIBundle\Helper\Date;

class Manga
{
    /**
     * The ID of the manga.
     *
     * @Type("integer")
     */
    private $id;

    /**
     * Title of the manga.
     *
     * @Type("string")
     */
    private $title;

    /**
     * Map of other titles for the manga.
     *
     * @Type("array<string, array<string>>")
     */
    private $otherTitles = array();

    /**
     * The global rank of the manga.
     *
     * @Type("integer")
     */
    private $rank;

    /**
     * Global rank of the manga based on popularity (number of people with the title on the list).
     *
     * @Type("integer")
     */
    private $popularityRank;

    /**
     * URL of tan image to the manga.
     *
     * @Type("string")
     */
    private $imageUrl;

    /**
     * Type of manga.
     *
     * Defined string from the type of manga. Value will be one of Manga, Novel, One Shot, Doujin, Manhwa, Manhua, OEL ("OEL manga" refers to "Original English-Language manga")
     *
     * @Type("string")
     */
    private $type;

    /**
     * Total number of chapters of the manga.
     *
     * This value is the number of chapters of the anime, or null if unknown.
     *
     * @Type("integer")
     */
    private $chapters;

    /**
     * Total number of volumes of the manga.
     *
     * This value is the number of volumes of the manga, or null if unknown.
     *
     * @Type("integer")
     */
    private $volumes;

    /**
     * Publishing status of the manga.
     *
     * Defines string of the status of the manga. Value will be one of finished, publishing, not yet published.
     *
     * @Type("string")
     */
    private $status;

    /**
     * Beginning date from which this manga/work was published.
     *
     * This is the starting date of the anime, formatted as an ISO8601-compatible string.
     * The contents my be formatted as a year, year and month, or year, month and day.
     * The value is null if the date is unknown.
     * Example: "2004-10-07", "2004-10", or "2004".
     *
     * @Type("string")
     * @Since("2.2")
     */
    private $startDate;

    /**
     * Publishing end date for the title.
     *
     * This is the starting date of the anime, formatted as an ISO8601-compatible string.
     * The contents my be formatted as a year, year and month, or year, month and day.
     * The value is null if the date is unknown.
     * Example: "2004-10-07", "2004-10", or "2004".
     *
     * @Type("string")
     * @Since("2.2")
     */
    private $endDate;

    /**
     * Weighted score of the manga.
     *
     * The score is calculated based on the ratings given by members.
     *
     * @Type("double")
     */
    private $membersScore;

    /**
     * The number of members that have the manga on the list.
     *
     * @Type("integer")
     */
    private $membersCount;

    /**
     * The number of members that have the manga marked as a favorite.
     *
     * @Type("integer")
     */
    private $favoritedCount;

    /**
     * External links with some information for the series.
     *
     * @Type("array<string, string>")
     * @Since("2.2")
     */
    private $externalLinks;

    /**
     * Description of the manga.
     *
     * An HTML-formatted string describing the anime
     *
     * @Type("string")
     */
    private $synopsis;

    /**
     * Background information of the anime.
     *
     * An HTML-formatted string describing the anime background
     *
     * @Type("string")
     * @Since("2.2")
     */
    private $background;

    /**
     * A list of genres for the manga.
     *
     * @Type("array<string>")
     */
    private $genres = array();

    /**
     * The Authors of the title.
     *
     * @Type("array")
     * @Since("2.2")
     */
    private $authors = array();

    /**
     * The name of the publication in which the title was published/serialized.
     *
     * @Type("string")
     * @Since("2.2")
     */
    private $serialization;
    /**
     * A list of popular tags for the manga.
     *
     * @Type("array<string>")
     */
    private $tags = array();

    /**
     * A list of relations returned by MAL, each containing a list of items for that relation.
     *
     * @Type("array")
     * @Since("2.2")
     */
    private $related = array();

    /**
     * A list of anime adaptations of this manga (or conversely, anime from which this manga is adapted).
     *
     * @deprecated
     * @Type("array")
     * @until("3.0")
     */
    private $animeAdaptations = array();

    /**
     * A list of related manga.
     *
     * @deprecated
     * @Type("array")
     * @until("3.0")
     */
    private $relatedManga = array();

    /**
     * A list of alternative versions of this manga.
     *
     * @deprecated
     * @Type("array")
     * @until("3.0")
     */
    private $alternativeVersions = array();

    /**
     * User's read status of the manga.
     *
     * This is a string that is one of: reading, completed, on-hold, dropped, plan to read
     *
     * @Type("string")
     */
    private $readStatus;

    /**
     * Number of chapters already read by the user.
     *
     * @Type("integer")
     */
    private $chaptersRead;

    /**
     * Number of volumes already read by the user.
     *
     * @Type("integer")
     */
    private $volumesRead;

    /**
     * User's score for the manga, from 1 to 10.
     *
     * @Type("integer")
     */
    private $score;

    /**
     * ID of the manga as per the user's list.
     *
     * API 1.0
     * This used to correspond to a unique ID for the title on the user's list, but is no longer used.
     *
     * @Type("integer")
     * @Until("1.1")
     */
    private $listedMangaId;

    /**
     * Tags assigned by the user.
     *
     * @Type("array<string>")
     * @Since("2.0")
     */
    private $personalTags = array();

    /**
     * The date the user started reading the title.
     *
     * @Type("DateTime<'Y-m-d'>")
     * @Since("2.0")
     */
    private $readingStart;

    /**
     * The date the user finished reading the title.
     *
     * @Type("DateTime<'Y-m-d'>")
     * @Since("2.0")
     */
    private $readingEnd;

    /**
     * Reading priority level for the title.
     *
     * Integer corresponding to the reading priority of the manga from 0 (low) to 2 (high).
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $priority;

    /**
     * The number of chapters downloaded by the user.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $chapDownloaded;

    /**
     * Set if the user is rerereading the manga.
     *
     * @Type("boolean")
     * @Since("2.0")
     */
    private $rereading;

    /**
     * The number of times the user has re-read the title. (Does not include the first time.).
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $rereadCount;

    /**
     * How much value the user thinks there is in rereading the series.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $rereadValue;

    /**
     * The user's personal comments on the title.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $personalComments;

    /**
     * The date of the last update from the user.
     *
     * This is the update date of the anime, formatted as an ISO8601 string.
     * This ISO8601 will allow seconds!
     *
     * @Type("string")
     * @Since("2.2")
     */
    private $lastUpdated;

    /**
     * Set the id property.
     *
     * @param int $id The id of series
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
     * @param string $title The title of series
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
     * @param array $otherTitles Other titles of series
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
     * @param int $rank The rank of series
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
     * @param int $popularityRank The Popularity Rank of series
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
     * @param string $imageUrl The Image url of series
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
     * @param int $type The type of series.
     *                  Can be 1/Manga, 2/Novel, 3/One Shot, 4/Doujin, 5/Manhwa, 6/Manhua, or 7/OEL. The default is "Manga".
     */
    public function setType($type)
    {
        switch ($type) {
            case 1:
            case 'Manga':
                $this->type = 'Manga';
                break;
            case 2:
            case 'Novel':
                $this->type = 'Novel';
                break;
            case 3:
            case 'One Shot':
            case 'One-shot':
                $this->type = 'One Shot';
                break;
            case 4:
            case 'Doujin':
            case 'Doujinshi':
                $this->type = 'Doujin';
                break;
            case 5:
            case 'Manwha': //Old MAL mis-spelling
            case 'Manhwa':
                $this->type = 'Manhwa'; //Korean comics
                break;
            case 6:
            case 'Manhua':
                $this->type = 'Manhua'; //Chinese comics
                break;
            case 7:
            case 'OEL':
                $this->type = 'OEL'; //Original English Language Manga
                break;
            default:
                $this->type = 'Manga';
                break;
        }
    }

    /**
     * Return the Type ID instead of string.
     *
     * @param $type The type which can be a string or int
     *
     * @return int The type ID
     */
    public static function getTypeId($type)
    {
        switch ($type) {
            case 1:
            case 'Manga':
                return 1;
            case 2:
            case 'Novel':
                return 2;
            case 3:
            case 'One Shot':
            case 'One-shot':
                return 3;
            case 4:
            case 'Doujin':
            case 'Doujinshi':
                return 4;
            case 5:
            case 'Manwha': //Old MAL mis-spelling
            case 'Manhwa':
                return 5; //Korean comics
            case 6:
            case 'Manhua':
                return 6; //Chinese comics
            case 7:
            case 'OEL':
                return 7; //Original English Language Manga
            default:
                return 0;
        }
    }

    /**
     * Get the type property.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the chapters property.
     *
     * @param int $chapters The number chapters of series
     */
    public function setChapters($chapters)
    {
        $this->chapters = $chapters;
    }

    /**
     * Get the episodes property.
     *
     * @return int
     */
    public function getChapters()
    {
        return $this->chapters;
    }

    /**
     * Set the volumes property.
     *
     * @param int $volumes The number volumes of series
     */
    public function setVolumes($volumes)
    {
        $this->volumes = $volumes;
    }

    /**
     * Get the volumes property.
     *
     * @return int
     */
    public function getVolumes()
    {
        return $this->volumes;
    }

    /**
     * Set the status property.
     *
     * @param int $status The publishing status of manga
     *                    Can be 1 (publishing), 2 (finished) or 3 (not yet published). The default is "2".
     */
    public function setStatus($status)
    {
        switch ($status) {
            case 1:
            case 'publishing':
                $this->status = 'publishing';
                break;
            case 2:
            case 'finished':
                $this->status = 'finished';
                break;
            case 3:
            case 'not yet published':
                $this->status = 'not yet published';
                break;
            default:
                $this->status = 'finished';
                break;
        }
    }

    /**
     * Get the status property.
     *
     * @return string (currently airing/finished airing/not yet aired)
     */
    public function getstatus()
    {
        return $this->status;
    }

    /**
     * Set the startDate property.
     *
     * @param \DateTime $startDate The start date of the series
     * @param string    $accuracy  To what level of accuracy this item is. May be "year", "month", or "day". Defaults to "day"
     */
    public function setStartDate(\DateTime $startDate, $accuracy = 'day')
    {
        switch ($accuracy) {
            case 'year':
                $this->startDate = $startDate->format('Y');
                break;
            case 'month':
                $this->startDate = $startDate->format('Y-m');
                break;
            case 'day':
            default:
                $this->startDate = $startDate->format('Y-m-d');
        }
    }

    /**
     * Get the startDate property.
     *
     * @return string (ISO 8601)
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set the endDate property.
     *
     * @param \DateTime $endDate  The end date of the series
     * @param string    $accuracy To what level of accuracy this item is. May be "year", "month", or "day". Defaults to "day"
     */
    public function setEndDate(\DateTime $endDate, $accuracy = 'day')
    {
        switch ($accuracy) {
            case 'year':
                $this->endDate = $endDate->format('Y');
                break;
            case 'month':
                $this->endDate = $endDate->format('Y-m');
                break;
            case 'day':
            default:
                $this->endDate = $endDate->format('Y-m-d');
        }
    }

    /**
     * Get the endDate property.
     *
     * @return string (ISO 8601)
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set the membersScore property.
     *
     * @param float $membersScore The score given by MAL members
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
     * @param int $membersCount The number of members that added this serie in their list
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
     * @param int $favoritedCount The number of members that added this serie in their favorite list
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
     * Set the externalLinks property.
     *
     * @param string $sourceName The website name which contains the info
     * @param string $sourceUrl  The direct website url to access info
     */
    public function setExternalLinks($sourceName, $sourceUrl)
    {
        $this->externalLinks[$sourceName] = $sourceUrl;
    }

    /**
     * Get the externalLinks property.
     *
     * @return array With information about external urls
     */
    public function getExternalLinks()
    {
        return $this->externalLinks;
    }

    /**
     * Set the synopsis property.
     *
     * @param string $synopsis The Text describing the anime
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
     * Set the background property.
     *
     * @param string $background The Text describing the anime
     */
    public function setBackground($background)
    {
        $this->background = $background;
    }

    /**
     * Get the background property.
     *
     * @return string
     */
    public function getBackground()
    {
        return $this->background;
    }

    /**
     * Set the genres property.
     *
     * @param string $genres The genres of series
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
     * Set the authors of the series.
     *
     * @param array $authors The authors, as an array
     */
    public function setAuthors($authors)
    {
        $this->authors = $authors;
    }

    /**
     * Return the list of authors.
     *
     * @return array
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Set the Serialization/Publisher.
     *
     * @param string $serialization The publication of serialization
     */
    public function setSerialization($serialization)
    {
        $this->serialization = $serialization;
    }

    /**
     * Get the serialization/publisher.
     *
     * @return string
     */
    public function getSerialization()
    {
        return $this->serialization;
    }

    /**
     * Set the tags property.
     *
     * @param string $tags The tags of series
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
     * Set the animeAdaptations property.
     *
     * @param string $animeAdaptations The anime adaptations of series
     */
    public function setAnimeAdaptations($animeAdaptations)
    {
        $this->animeAdaptations[] = $animeAdaptations;
    }

    /**
     * Get the animeAdaptations property.
     *
     * @return array
     */
    public function getAnimeAdaptations()
    {
        return $this->animeAdaptations;
    }

    /**
     * Set the relatedManga property.
     *
     * @param string $relatedManga The related mangas of series
     */
    public function setRelatedManga($relatedManga)
    {
        $this->relatedManga[] = $relatedManga;
    }

    /**
     * Get the relatedManga property.
     *
     * @return array
     */
    public function getRelatedManga()
    {
        return $this->relatedManga;
    }

    /**
     * Set the alternativeVersions property.
     *
     * @param string $alternativeVersions The alternative versions of series
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
     * Add a relation.
     *
     * @param array  $item An array containing details of the related item
     * @param string $type The type of relation, all lowercase with the underscore replacing spaces
     */
    public function addRelation($item, $type)
    {
        $this->related[$type][] = $item;

        switch ($type) {
            case 'adaptation':
                $this->setAnimeAdaptations($item);
                break;
            case 'alternative_version':
                $this->setAlternativeVersions($item);
                break;
            default:
                $this->setRelatedManga($item);
                break;
        }
    }

    /**
     * Get the array of relations.
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * Set the readStatus propery.
     *
     * @param string|int $statusid The input status value of an item Accepts either integers as defined
     *                             by the MAL API module, or strings as defined by the Ruby API (mal-api.com).
     */
    public function setReadStatus($statusid)
    {
        switch ($statusid) {
            case 1:
            case 'reading':
                $this->readStatus = 'reading';
                break;
            case 2:
            case 'completed':
                $this->readStatus = 'completed';
                break;
            case 3:
            case 'on-hold':
            case 'onhold':
                $this->readStatus = 'on-hold';
                break;
            case 4:
            case 'dropped':
                $this->readStatus = 'dropped';
                break;
            case 6:
            case 'plan to read':
            case 'plantoread':
                $this->readStatus = 'plan to read';
                break;
            default:
                $this->readStatus = 'reading';
                break;
        }
    }

    /**
     * Get the current value of the read status.
     *
     * @param string $type What type you want to get back. Currently accepts either "string" or "int"
     *                     Defaults to "string".
     *
     * @return string|int
     */
    public function getReadStatus($type = 'string')
    {
        if ($type == 'int') {
            switch ($this->readStatus) {
                case 'completed':
                    $status = 2;
                    break;
                case 'on-hold':
                    $status = 3;
                    break;
                case 'dropped':
                    $status = 4;
                    break;
                case 'plan to read':
                    $status = 6;
                    break;
                case 'reading':
                default:
                    $status = 1;
                    break;
            }

            return $status;
        } else {
            return $this->readStatus;
        }
    }

    /**
     * Set the chaptersRead property.
     *
     * @param string chaptersRead The number of read chapters
     */
    public function setChaptersRead($chaptersRead)
    {
        $this->chaptersRead = $chaptersRead;
    }

    /**
     * Get the chaptersRead property.
     *
     * @return int
     */
    public function getChaptersRead()
    {
        return $this->chaptersRead;
    }

    /**
     * Set the volumesRead property.
     *
     * @param string volumesRead The number of read volumes
     */
    public function setVolumesRead($volumesRead)
    {
        $this->volumesRead = $volumesRead;
    }

    /**
     * Get the volumesRead property.
     *
     * @return int
     */
    public function getVolumesRead()
    {
        return $this->volumesRead;
    }

    /**
     * Set the score property.
     *
     * @param int $score The score of series
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
     * Set the listedMangaId property.
     *
     * @param int $listedMangaId For internal use
     */
    public function setListedMangaId($listedMangaId)
    {
        $this->listedMangaId = $listedMangaId;
    }

    /**
     * Get the listedMangaId property.
     *
     * @return int
     */
    public function getListedMangaId()
    {
        return $this->listedMangaId;
    }

    public function getPersonalTags()
    {
        return $this->personalTags;
    }

    public function setPersonalTags($tags)
    {
        $this->personalTags = $tags;
    }

    public function getReadingStart()
    {
        return $this->readingStart;
    }

    public function setReadingStart($date)
    {
        $this->readingStart = $date;
    }

    public function getReadingEnd()
    {
        return $this->readingEnd;
    }

    public function setReadingEnd($date)
    {
        $this->readingEnd = $date;
    }

    /**
     * Get the current reading priority for the anime title.
     *
     * @param string $type What type you want to get back
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
                case 2:
                    return 'High';
                default:
                    return 'Low';
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

    public function getChapDownloaded()
    {
        return $this->chapDownloaded;
    }

    public function setChapDownloaded($downloaded)
    {
        $this->chapDownloaded = (int) $downloaded;
    }

    public function getRereading()
    {
        return $this->rereading;
    }

    public function setRereading($rereading)
    {
        $this->rereading = (bool) $rereading;
    }

    public function getRereadCount()
    {
        return $this->rereadCount;
    }

    public function setRereadCount($count)
    {
        $this->rereadCount = (int) $count;
    }

    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = Date::formatTime($lastUpdated);
    }

    /**
     * Get the current reading priority for the title.
     *
     * @param string $type What type you want to get back
     *                     Currently accepts either "string" or "int". Defaults to "int".
     *
     * @return string|int
     */
    public function getRereadValue($type = 'int')
    {
        if ($type == 'string') {
            switch ($this->rereadValue) {
                case 1:
                    return 'Very Low';
                case 2:
                    return 'Low';
                case 3:
                    return 'Medium';
                case 4:
                    return 'High';
                case 5:
                    return 'Very High';
                default:
                    return;
            }
        } else {
            return $this->rereadValue;
        }
    }

    public function setRereadValue($value)
    {
        $this->rereadValue = (int) $value;
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
     * @return string An XML document of manga values as defined at http://myanimelist.net/modules.php?go=api#mangavalues
     */
    public function MALApiXml($update_items)
    {
        //For now, just add in the parameters we will use. The MAL API will handle missing items just fine.
        $xml = new \SimpleXMLElement('<entry/>');

        if (in_array('chapters', $update_items)) {
            $xml->addChild('chapter', $this->getChaptersRead());
        }

        if (in_array('volumes', $update_items)) {
            $xml->addChild('volume', $this->getVolumesRead());
        }

        if (in_array('status', $update_items)) {
            $xml->addChild('status', $this->getReadStatus('int')); //Use int for the MAL API to eliminate problems with strings.
        }

        if (in_array('score', $update_items)) {
            $xml->addChild('score', $this->getScore());
        }

        if (in_array('downloaded', $update_items)) {
            $xml->addChild('downloaded_chapters', $this->getChapDownloaded());
        }

        if (in_array('isRereading', $update_items)) {
            $xml->addChild('enable_rereading', $this->getRereading());
        }

        if (in_array('rereadCount', $update_items)) {
            $xml->addChild('times_reread', $this->getRereadCount());
        }

        if (in_array('rereadValue', $update_items)) {
            $xml->addChild('reread_value', $this->getRereadValue('int'));
        }

        if (in_array('start', $update_items)) {
            // Date must be MMDDYYYY.
            $xml->addChild('date_start', $this->getReadingStart()->format('mdY'));
        }

        if (in_array('end', $update_items)) {
            // Date must be MMDDYYY.
            $xml->addChild('date_finish', $this->getReadingEnd()->format('mdY'));
        }

        if (in_array('priority', $update_items)) {
            $xml->addChild('priority', $this->getPriority('int'));
        }

        if (in_array('comments', $update_items)) {
            $xml->addChild('comments', $this->getPersonalComments());
        }

        if (in_array('tags', $update_items)) {
            $xml->addChild('tags', $this->getPersonalTags());
        }

        return $xml->asXML();
    }

    /**
     * Returns a int array for MAL URL requests.
     *
     * @param $genreString a string of genres separated by ','
     *
     * @return string Int array for MAL URL requests
     */
    public static function getGenresId($genreString)
    {
        $genreArray = explode(',', str_replace(', ', ',', strtolower($genreString)));
        $genreIds = array();
        if ($genreArray[0] !== '') {
            foreach ($genreArray as $genre) {
                switch ($genre) {
                    case 1:
                    case 'action':
                        $genreIds[] = 1;
                        break;
                    case 2:
                    case 'adventure':
                        $genreIds[] = 2;
                        break;
                    case 3:
                    case 'cars':
                        $genreIds[] = 3;
                        break;
                    case 4:
                    case 'comedy':
                        $genreIds[] = 4;
                        break;
                    case 5:
                    case 'dementia':
                        $genreIds[] = 5;
                        break;
                    case 6:
                    case 'demons':
                        $genreIds[] = 6;
                        break;
                    case 7:
                    case 'mystery':
                        $genreIds[] = 7;
                        break;
                    case 8:
                    case 'drama':
                        $genreIds[] = 8;
                        break;
                    case 9:
                    case 'ecchi':
                        $genreIds[] = 9;
                        break;
                    case 10:
                    case 'fantasy':
                        $genreIds[] = 10;
                        break;
                    case 11:
                    case 'game':
                        $genreIds[] = 11;
                        break;
                    case 12:
                    case 'hentai':
                        $genreIds[] = 12;
                        break;
                    case 13:
                    case 'historical':
                        $genreIds[] = 13;
                        break;
                    case 14:
                    case 'horror':
                        $genreIds[] = 14;
                        break;
                    case 15:
                    case 'kids':
                        $genreIds[] = 15;
                        break;
                    case 16:
                    case 'magic':
                        $genreIds[] = 16;
                        break;
                    case 17:
                    case 'martial arts':
                        $genreIds[] = 17;
                        break;
                    case 18:
                    case 'mecha':
                        $genreIds[] = 18;
                        break;
                    case 19:
                    case 'music':
                        $genreIds[] = 19;
                        break;
                    case 20:
                    case 'parody':
                        $genreIds[] = 20;
                        break;
                    case 21:
                    case 'samurai':
                        $genreIds[] = 21;
                        break;
                    case 22:
                    case 'romance':
                        $genreIds[] = 22;
                        break;
                    case 23:
                    case 'school':
                        $genreIds[] = 23;
                        break;
                    case 24:
                    case 'sci-fi':
                        $genreIds[] = 24;
                        break;
                    case 25:
                    case 'shoujo':
                        $genreIds[] = 25;
                        break;
                    case 26:
                    case 'shoujo ai':
                        $genreIds[] = 26;
                        break;
                    case 27:
                    case 'shounen':
                        $genreIds[] = 27;
                        break;
                    case 28:
                    case 'shounen ai':
                        $genreIds[] = 28;
                        break;
                    case 29:
                    case 'space':
                        $genreIds[] = 29;
                        break;
                    case 30:
                    case 'sports':
                        $genreIds[] = 30;
                        break;
                    case 31:
                    case 'super power':
                        $genreIds[] = 31;
                        break;
                    case 32:
                    case 'vampire':
                        $genreIds[] = 32;
                        break;
                    case 33:
                    case 'yaoi':
                        $genreIds[] = 33;
                        break;
                    case 34:
                    case 'yuri':
                        $genreIds[] = 34;
                        break;
                    case 35:
                    case 'harem':
                        $genreIds[] = 35;
                        break;
                    case 36:
                    case 'slice of life':
                        $genreIds[] = 36;
                        break;
                    case 37:
                    case 'supernatural':
                        $genreIds[] = 37;
                        break;
                    case 38:
                    case 'military':
                        $genreIds[] = 38;
                        break;
                    case 39:
                    case 'police':
                        $genreIds[] = 39;
                        break;
                    case 40:
                    case 'psychological':
                        $genreIds[] = 40;
                        break;
                    case 41:
                    case 'seinen':
                        $genreIds[] = 41;
                        break;
                    case 42:
                    case 'josei':
                        $genreIds[] = 42;
                        break;
                    case 43:
                    case 'doujinshi':
                        $genreIds[] = 43;
                        break;
                    case 44:
                    case 'gender bender':
                        $genreIds[] = 44;
                        break;
                    case 45:
                    case 'thriller':
                        $genreIds[] = 45;
                        break;
                    default:
                        break;
                }
            }

            return '&genre[]='.implode('&genre[]=', $genreIds);
        } else {
            return '';
        }
    }

    /**
     * Returns the right sorting ID.
     *
     * @param $sort The parameter with the sorting type
     *
     * @return int The sortType supported by MAL
     */
    public static function getColumnId($sort)
    {
        switch (strtolower($sort)) {
            case 1:
            case 'title':
                return 1;
            case 2:
            case 'startdate':
            case 'start date':
                return 2;
            case 3:
            case 'score':
                return 3;
            case 4:
            case 'volume':
                return 4;
            case 5:
            case 'enddate':
            case 'end date':
                return 5;
            case 6:
            case 'chapter':
                return 6;
            case 7:
            case 'members':
                return 7;
            case 8:
            case 'type':
                return 8;
            case 9:
            case 'id':
                return 9;
            default:
                return 0;
        }
    }
}
