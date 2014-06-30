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

class Manga
{
    private $id; //The manga ID.
    private $title; //The manga title.
    private $otherTitles = array(); //A hash/dictionary containing other titles this manga has.
    private $rank; //Global rank of this manga. Not available in /mangalist requests.
    private $popularityRank; //Rank of this manga based on its popularity, i.e. number of users that have added this manga. Not available in /mangalist requests.
    private $imageUrl; //URL to an image for this manga.
    private $type; //Type of manga. Possible values: Manga, Novel, One Shot, Doujin, Manwha, Manhua, OEL ("OEL manga" refers to "Original English-Language manga").
    private $chapters; //Number of chapters. null is returned if the number of chapters is unknown.
    private $volumes; //Number of volumes. null is returned if the number of volumes is unknown.
    private $status; //Publishing status of this manga. Possible values: finished, publishing, not yet published.
    private $membersScore; //Weighted score members of MyAnimeList have given to this manga. Not available in /mangalist requests.
    private $membersCount; //Number of members who have this manga on their list. Not available in /mangalist requests.
    private $favoritedCount; //Number of members who have this manga marked as one of their favorites. Not available in /mangalist requests.
    private $synopsis; //Text describing the manga. Not available in /mangalist requests.
    private $genres = array(); //A list of genres for this manga, e.g. ["Comedy", "Slice of Life"]. Not available in /mangalist requests.
    private $tags = array(); //A list of popular tags for this manga, e.g. ["comedy", "slice of life"]. Not available in /mangalist requests.
    private $animeAdaptations = array(); //A list of anime adaptations of this manga (or conversely, anime from which this manga is adapted). Not available in /mangalist requests.
    private $relatedManga = array(); //A list of related manga.
    private $alternativeVersions = array(); //A list of alternative versions of this manga.
    private $readStatus; //User's read status of the anime. This is a string that is one of: reading, completed, on-hold, dropped, plan to read.
    private $chaptersRead; //Number of chapters already read by the user.
    private $volumesRead; //Number of volumes already read by the user.
    private $score; //User's score for the manga, from 1 to 10.
    private $listedMangaId; //For internal use. This is not listed as a public part of the returned list and it seems to only be used internally in the Ruby API.

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
     * Set the otherTitles property
     *
     * @param array $otherTitles Other titles of series.
     *
     * @return void
     */
    public function setOtherTitles($otherTitles)
    {
        $this->otherTitles = $otherTitles;
    }

    /**
     * Get the otherTitles property
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
     * Set the popularityRank property
     *
     * @param int $popularityRank The Popularity Rank of series.
     *
     * @return void
     */
    public function setPopularityRank($popularityRank)
    {
        $this->popularityRank = $popularityRank;
    }

    /**
     * Get the popularityRank property
     *
     * @return int
     */
    public function getPopularityRank()
    {
       return $this->popularityRank;
    }

    /**
     * Set the imageUrl property
     *
     * @param string $imageUrl The Image url of series.
     *
     * @return void
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * Get the imageUrl property
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
     * @param int $type The type of series.
     *     Can be 1/Manga, 2/Novel, 3/One Shot, 4/Doujin, 5/Manwha, 6/Manhua, or 7/OEL. The default is "Manga".
     *
     * @return void
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
                $this->type = 'One Shot';
                break;
            case 4:
            case 'Doujin':
                $this->type = 'Doujin';
                break;
            case 5:
            case 'Manwha':
                $this->type = 'Manwha'; //Korean comics
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
     * Get the type property
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the chapters property
     *
     * @param int $chapters The number chapters of series.
     *
     * @return void
     */
    public function setChapters($chapters)
    {
        $this->chapters = $chapters;
    }

    /**
     * Get the episodes property
     *
     * @return int
     */
    public function getChapters()
    {
       return $this->chapters;
    }

    /**
     * Set the volumes property
     *
     * @param int $volumes The number volumes of series.
     *
     * @return void
     */
    public function setVolumes($volumes)
    {
        $this->volumes = $volumes;
    }

    /**
     * Get the volumes property
     *
     * @return int
     */
    public function getVolumes()
    {
       return $this->volumes;
    }

    /**
     * Set the status property
     *
     * @param int $status The publishing status of manga.
     *     Can be 1 (publishing), 2 (finished) or 3 (not yet published). The default is "2".
     *
     * @return void
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
     * Get the status property
     *
     * @return string (currently airing/finished airing/not yet aired)
     */
    public function getstatus()
    {
       return $this->status;
    }

    /**
     * Set the membersScore property
     *
     * @param float $membersScore The score given by MAL members.
     *
     * @return void
     */
    public function setMembersScore($membersScore)
    {
        $this->membersScore = $membersScore;
    }

    /**
     * Get the membersScore property
     *
     * @return float
     */
    public function getMembersScore()
    {
        return $this->membersScore;
    }

    /**
     * Set the membersCount property
     *
     * @param int $membersCount The number of members that added this serie in their list.
     *
     * @return void
     */
    public function setMembersCount($membersCount)
    {
        $this->membersCount = $membersCount;
    }

    /**
     * Get the membersCount property
     *
     * @return int
     */
    public function getMembersCount()
    {
        return $this->membersCount;
    }

    /**
     * Set the favoritedCount property
     *
     * @param int $favoritedCount The number of members that added this serie in their favorite list.
     *
     * @return void
     */
    public function setFavoritedCount($favoritedCount)
    {
        $this->favoritedCount = $favoritedCount;
    }

    /**
     * Get the favoritedCount property
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
     * Set the animeAdaptations property
     *
     * @param string $animeAdaptations The anime adaptations of series.
     *
     * @return void
     */
    public function setAnimeAdaptations($animeAdaptations)
    {
        $this->animeAdaptations[] = $animeAdaptations;
    }

    /**
     * Get the animeAdaptations property
     *
     * @return array
     */
    public function getAnimeAdaptations()
    {
        return $this->animeAdaptations;
    }

    /**
     * Set the relatedManga property
     *
     * @param string $relatedManga The related mangas of series.
     *
     * @return void
     */
    public function setRelatedManga($relatedManga)
    {
        $this->relatedManga[] = $relatedManga;
    }

    /**
     * Get the relatedManga property
     *
     * @return array
     */
    public function getRelatedManga()
    {
        return $this->relatedManga;
    }

    /**
     * Set the alternativeVersions property
     *
     * @param string $alternativeVersions The alternative versions of series.
     *
     * @return void
     */
    public function setAlternativeVersions($alternativeVersions)
    {
        $this->alternativeVersions[] = $alternativeVersions;
    }

    /**
     * Get the alternativeVersions property
     *
     * @return array
     */
    public function getAlternativeVersions()
    {
        return $this->alternativeVersions;
    }

    /**
     * Set the readStatus propery
     *
     * @param string|int $status The input status value of an item. Accepts either integers as defined
     *     by the MAL API module, or strings as defined by the Ruby API (mal-api.com).
     *
     * @return void
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
     * @param string $type What type you want to get back. Currently accepts either "string" or "int".
     *     Defaults to "string".
     *
     * @return string|int
     */
    public function getReadStatus($type = 'string')
    {
        if ($type == 'int') {
            switch ($this->readStatus) {
                case 'reading':
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
                case 'plan to read':
                    return 6;
                    break;
                default:
                    return 1;
                    break;
            }
        } else {
            return $this->readStatus;
        }
    }

    /**
     * Set the chaptersRead property
     *
     * @param string chaptersRead The number of read chapters.
     *
     * @return void
     */
    public function setChaptersRead($chaptersRead)
    {
        $this->chaptersRead = $chaptersRead;
    }

    /**
     * Get the chaptersRead property
     *
     * @return int
     */
    public function getChaptersRead()
    {
        return $this->chaptersRead;
    }

    /**
     * Set the volumesRead property
     *
     * @param string volumesRead The number of read volumes.
     *
     * @return void
     */
    public function setVolumesRead($volumesRead)
    {
        $this->volumesRead = $volumesRead;
    }

    /**
     * Get the volumesRead property
     *
     * @return int
     */
    public function getVolumesRead()
    {
        return $this->volumesRead;
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
     * Set the listedMangaId property
     *
     * @param int $listedMangaId For internal use.
     *
     * @return void
     */
    public function setListedMangaId($listedMangaId)
    {
        $this->listedMangaId = $listedMangaId;
    }

    /**
     * Get the listedMangaId property
     *
     * @return int
     */
    public function getListedMangaId()
    {
        return $this->listedMangaId;
    }

    /**
     * Return a formatted XML document for updating MAL
     *
     * @return string An XML document of manga values as defined at http://myanimelist.net/modules.php?go=api#mangavalues
     */
    public function MALApiXml()
    {
        //For now, just add in the parameters we will use. The MAL API will handle missing items just fine.
        $xml = new \SimpleXMLElement('<entry/>');

        $xml->addChild('chapter', $this->getChaptersRead());
        $xml->addChild('volume', $this->getVolumesRead());
        $xml->addChild('status', $this->getReadStatus('int')); //Use int for the MAL API to eliminate problems with strings.
        $xml->addChild('score', $this->getScore());

        return $xml->asXML();
    }

}
