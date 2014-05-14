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
    private $other_titles = array(); //A hash/dictionary containing other titles this manga has.
    private $rank; //Global rank of this manga. Not available in /mangalist requests.
    private $popularity_rank; //Rank of this manga based on its popularity, i.e. number of users that have added this manga. Not available in /mangalist requests.
    private $image_url; //URL to an image for this manga.
    private $type; //Type of manga. Possible values: Manga, Novel, One Shot, Doujin, Manwha, Manhua, OEL ("OEL manga" refers to "Original English-Language manga").
    private $chapters; //Number of chapters. null is returned if the number of chapters is unknown.
    private $volumes; //Number of volumes. null is returned if the number of volumes is unknown.
    private $status; //Publishing status of this manga. Possible values: finished, publishing, not yet published.
    private $members_score; //Weighted score members of MyAnimeList have given to this manga. Not available in /mangalist requests.
    private $members_count; //Number of members who have this manga on their list. Not available in /mangalist requests.
    private $favorited_count; //Number of members who have this manga marked as one of their favorites. Not available in /mangalist requests.
    private $synopsis; //Text describing the manga. Not available in /mangalist requests.
    private $genres = array(); //A list of genres for this manga, e.g. ["Comedy", "Slice of Life"]. Not available in /mangalist requests.
    private $tags = array(); //A list of popular tags for this manga, e.g. ["comedy", "slice of life"]. Not available in /mangalist requests.
    private $anime_adaptations = array(); //A list of anime adaptations of this manga (or conversely, anime from which this manga is adapted). Not available in /mangalist requests.
    private $related_manga = array(); //A list of related manga.
    private $alternative_versions = array(); //A list of alternative versions of this manga.
    private $read_status; //User's read status of the anime. This is a string that is one of: reading, completed, on-hold, dropped, plan to read.
    private $chapters_read; //Number of chapters already read by the user.
    private $volumes_read; //Number of volumes already read by the user.
    private $score; //User's score for the manga, from 1 to 10.
    private $listed_manga_id; //For internal use. This is not listed as a public part of the returned list and it seems to only be used internally in the Ruby API.

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
        $this->other_titles = $other_titles;
    }

    /**
     * Get the other_titles property
     *
     * @return array
     */
    public function getOtherTitles()
    {
       return $this->other_titles;
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
        $this->popularity_rank = $popularity_rank;
    }

    /**
     * Get the popularity_rank property
     *
     * @return int
     */
    public function getPopularityRank()
    {
       return $this->popularity_rank;
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
        $this->image_url = $image_url;
    }

    /**
     * Get the image_url property
     *
     * @return string
     */
    public function getImageUrl()
    {
       return $this->image_url;
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
     * Set the members_score property
     *
     * @param float $members_score The score given by MAL members.
     *
     * @return void
     */
    public function setMembersScore($members_score)
    {
        $this->members_score = $members_score;
    }

    /**
     * Get the members_score property
     *
     * @return float
     */
    public function getMembersScore()
    {
        return $this->members_score;
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
        $this->members_count = $members_count;
    }

    /**
     * Get the members_count property
     *
     * @return int
     */
    public function getMembersCount()
    {
        return $this->members_count;
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
        $this->favorited_count = $favorited_count;
    }

    /**
     * Get the favorited_count property
     *
     * @return int
     */
    public function getFavoritedCount()
    {
        return $this->favorited_count;
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
     * Set the anime_adaptations property
     *
     * @param string $anime_adaptations The anime adaptations of series.
     *
     * @return void
     */
    public function setAnimeAdaptations($anime_adaptations)
    {
        $this->anime_adaptations[] = $anime_adaptations;
    }

    /**
     * Get the anime_adaptations property
     *
     * @return array
     */
    public function getAnimeAdaptations()
    {
        return $this->anime_adaptations;
    }

    /**
     * Set the related_manga property
     *
     * @param string $related_manga The related mangas of series.
     *
     * @return void
     */
    public function setRelatedManga($related_manga)
    {
        $this->related_manga[] = $related_manga;
    }

    /**
     * Get the related_manga property
     *
     * @return array
     */
    public function getRelatedManga()
    {
        return $this->related_manga;
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
        $this->alternative_versions[] = $alternative_versions;
    }

    /**
     * Get the alternative_versions property
     *
     * @return array
     */
    public function getAlternativeVersions()
    {
        return $this->alternative_versions;
    }

    /**
     * Set the read_status propery
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
                $this->read_status = 'reading';
                break;
            case 2:
            case 'completed':
                $this->read_status = 'completed';
                break;
            case 3:
            case 'on-hold':
            case 'onhold':
                $this->read_status = 'on-hold';
                break;
            case 4:
            case 'dropped':
                $this->read_status = 'dropped';
                break;
            case 6:
            case 'plan to read':
            case 'plantoread':
                $this->read_status = 'plan to read';
                break;
            default:
                $this->read_status = 'reading';
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
            switch ($this->read_status) {
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
            return $this->read_status;
        }
    }

    /**
     * Set the chapters_read property
     *
     * @param string chapters_read The number of read chapters.
     *
     * @return void
     */
    public function setChaptersRead($chapters_read)
    {
        $this->chapters_read = $chapters_read;
    }

    /**
     * Get the chapters_read property
     *
     * @return int
     */
    public function getChaptersRead()
    {
        return $this->chapters_read;
    }

    /**
     * Set the volumes_read property
     *
     * @param string volumes_read The number of read volumes.
     *
     * @return void
     */
    public function setVolumesRead($volumes_read)
    {
        $this->volumes_read = $volumes_read;
    }

    /**
     * Get the volumes_read property
     *
     * @return int
     */
    public function getVolumesRead()
    {
        return $this->volumes_read;
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
     * Set the listed_manga_id property
     *
     * @param int $listed_manga_id For internal use.
     *
     * @return void
     */
    public function setListedMangaId($listed_manga_id)
    {
        $this->listed_manga_id = $listed_manga_id;
    }

    /**
     * Get the listed_manga_id property
     *
     * @return int
     */
    public function getListedMangaId()
    {
        return $this->listed_manga_id;
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
