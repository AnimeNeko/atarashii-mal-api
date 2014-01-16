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
                $this->type = 'Manga';
                break;
            case 2:
                $this->type = 'Novel';
                break;
            case 3:
                $this->type = 'One Shot';
                break;
            case 4:
                $this->type = 'Doujin';
                break;
            case 5:
                $this->type = 'Manwha'; //Korean comics
                break;
            case 6:
                $this->type = 'Manhua'; //Chinese comics
                break;
            case 7:
                $this->type = 'OEL'; //Original English Language Manga
                break;
            default:
                $this->type = 'Manga';
                break;
        }
    }

    /**
     * Set the read_status propery
     *
     * @param string|int $status The input status value of an item. Accepts either integers as defined
     *         by the MAL API module, or strings as defined by the Ruby API (mal-api.com).
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
     *         Defaults to "string".
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
                $this->status = 'publishing';
                break;
            case 2:
                $this->status = 'finished';
                break;
            case 3:
                $this->status = 'not yet published';
                break;
            default:
                $this->status = 'finished';
                break;
        }
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

        $xml->addChild('chapter', $this->chapters_read);
        $xml->addChild('volume', $this->volumes_read);
        $xml->addChild('status', $this->getReadStatus('int')); //Use int for the MAL API to eliminate problems with strings.
        $xml->addChild('score', $this->score);

        return $xml->asXML();
    }

}
