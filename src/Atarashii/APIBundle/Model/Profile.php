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

/**
* An instance of this class represents a single user's profile
*/
class Profile
{
    /**
    * Fully qualified URL to the user's avatar image
    *
    * @var string
    */
    private $avatarUrl;

    /**
    * A ProfileDetails object containing general information on the user
    *
    * @var ProfileDetails
    */
    public $details;

    /**
    * An AnimeStats object containing information on the user's anime statistics
    *
    * @var AnimeStats
    */
    public $anime_stats;

    /**
    * A MangaStats object containing information on the user's manga statistics
    *
    * @var MangaStats
    */
    public $manga_stats;

    /**
    * Create an instance of the object
    */
    public function __construct()
    {
        // Initialize the sub classes we use.
        $this->details = new ProfileDetails();
        $this->anime_stats = new AnimeStats();
        $this->manga_stats = new MangaStats();
    }

    /**
     * Set the avatarUrl property
     *
     * @param string $avatarUrl The avatar url of an user.
     *
     * @return void
     */
    public function setAvatarUrl($avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;
    }

    /**
     * Get the avatarUrl property
     *
     * @return string
     */
    public function getAvatarUrl()
    {
       return $this->avatarUrl;
    }

}

/**
* This class is used within the Profile Class.
* It contains general user details
*/
class ProfileDetails
{
    private $lastOnline;
    private $status;
    private $gender;
    private $birthday;
    private $location;
    private $website;
    private $joinDate;
    private $accessRank;
    private $animeListViews = 0;
    private $mangaListViews = 0;
    private $forumPosts = 0;
    private $aim;
    private $comments = 0;
    private $msn;
    private $yahoo;

    /**
     * Set the lastOnline property
     *
     * @param string $lastOnline The last time when an user was online.
     *
     * @return void
     */
    public function setLastOnline($lastOnline)
    {
        $this->lastOnline = (new Date)->formatTime($lastOnline);
    }

    /**
     * Get the lastOnline property
     *
     * @return string
     */
    public function getLastOnline()
    {
       return $this->lastOnline;
    }

    /**
     * Set the status property
     *
     * @param string $status The status of an user.
     *
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get the status property.
     *
     * @return string
     */
    public function getStatus()
    {
       return $this->status;
    }

    /**
     * Set the gender property
     *
     * @param string $gender The gender of an user.
     *
     * @return void
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * Get the gender property
     *
     * @return string
     */
    public function getGender()
    {
       return $this->gender;
    }

    /**
     * Set the birthday property
     *
     * @param string $birthday The birthday of an user.
     *
     * @return void
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * Get the birthday property
     *
     * @return string
     */
    public function getBirthday()
    {
       return $this->birthday;
    }

    /**
     * Set the location property
     *
     * @param string $location The location of an user.
     *
     * @return void
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Get the location property
     *
     * @return string
     */
    public function getLocation()
    {
       return $this->location;
    }

    /**
     * Set the website property
     *
     * @param string $website The website of an user.
     *
     * @return void
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * Get the website property
     *
     * @return string
     */
    public function getWebsite()
    {
       return $this->website;
    }

    /**
     * Set the joinDate property
     *
     * @param string $joinDate The MAL join date of an user.
     *
     * @return void
     */
    public function setJoinDate($joinDate)
    {
        $this->joinDate = (new Date)->formatTime($joinDate);
    }

    /**
     * Get the joinDate property
     *
     * @return string
     */
    public function getJoinDate()
    {
       return $this->joinDate;
    }

    /**
     * Set the accessRank property
     *
     * @param string $accessRank The MAL access rank of an user.
     *
     * @return void
     */
    public function setAccessRank($accessRank)
    {
        $this->accessRank = $accessRank;
    }

    /**
     * Get the accessRank property
     *
     * @return string
     */
    public function getAccessRank()
    {
       return $this->accessRank;
    }

    /**
     * Set the animeListViews property
     *
     * @param int $animeListViews The animelist views of an user.
     *
     * @return void
     */
    public function setAnimeListViews($animeListViews)
    {
        $this->animeListViews = $animeListViews;
    }

    /**
     * Get the animeListViews property
     *
     * @return int
     */
    public function getAnimeListViews()
    {
       return $this->animeListViews;
    }

    /**
     * Set the mangaListViews property
     *
     * @param int $mangaListViews The mangalist views of an user.
     *
     * @return void
     */
    public function setMangaListViews($mangaListViews)
    {
        $this->mangaListViews = $mangaListViews;
    }

    /**
     * Get the mangaListViews property
     *
     * @return int
     */
    public function getMangaListViews()
    {
       return $this->mangaListViews;
    }

    /**
     * Set the forumPosts property
     *
     * @param int $forumPosts The number forum posts of an user.
     *
     * @return void
     */
    public function setForumPosts($forumPosts)
    {
        $this->forumPosts = $forumPosts;
    }

    /**
     * Get the forumPosts property
     *
     * @return int
     */
    public function getForumPosts()
    {
       return $this->forumPosts;
    }

    /**
     * Set the aim property
     *
     * @param string $aim The aim of an user.
     *
     * @return void
     */
    public function setAim($aim)
    {
        $this->aim = $aim;
    }

    /**
     * Get the aim property
     *
     * @return string
     */
    public function getAim()
    {
       return $this->aim;
    }

    /**
     * Set the comments property
     *
     * @param int $comments The number comments of an user.
     *
     * @return void
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * Get the comments property
     *
     * @return int
     */
    public function getComments()
    {
       return $this->comments;
    }

    /**
     * Set the msn property
     *
     * @param string $msn The msn of an user.
     *
     * @return void
     */
    public function setMsn($msn)
    {
        $this->msn = $msn;
    }

    /**
     * Get the msn property
     *
     * @return string
     */
    public function getMsn()
    {
       return $this->msn;
    }

    /**
     * Set the yahoo property
     *
     * @param string $yahoo The yahoo of an user.
     *
     * @return void
     */
    public function setYahoo($yahoo)
    {
        $this->yahoo = $yahoo;
    }

    /**
     * Get the yahoo property
     *
     * @return string
     */
    public function getYahoo()
    {
       return $this->yahoo;
    }
}

/**
* This class is used within the Profile Class.
* It contains statistical information on the user's anime watching.
*/
class AnimeStats
{
    private $timeDays;
    private $watching;
    private $completed;
    private $onHold;
    private $dropped;
    private $planToWatch;
    private $totalEntries;

    /**
     * Set the timeDays property
     *
     * @param float $timeDays The time of days of profiles.
     *
     * @return void
     */
    public function setTimeDays($timeDays)
    {
        $this->timeDays = $timeDays;
    }

    /**
     * Get the timeDays property
     *
     * @return float
     */
    public function getTimeDays()
    {
       return $this->timeDays;
    }

    /**
     * Set the watching property
     *
     * @param int $reading The number of watching animes.
     *
     * @return void
     */
    public function setWatching($watching)
    {
        $this->watching = $watching;
    }

    /**
     * Get the watching property
     *
     * @return int
     */
    public function getWatching()
    {
       return $this->watching;
    }

    /**
     * Set the completed property
     *
     * @param int $completed The number of completed animes.
     *
     * @return void
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;
    }

    /**
     * Get the completed property
     *
     * @return int
     */
    public function getCompleted()
    {
       return $this->completed;
    }

    /**
     * Set the onHold property
     *
     * @param int $onHold The number of onHold animes.
     *
     * @return void
     */
    public function setOnHold($onHold)
    {
        $this->onHold = $onHold;
    }

    /**
     * Get the onHold property
     *
     * @return int
     */
    public function getOnHold()
    {
       return $this->onHold;
    }

    /**
     * Set the dropped property
     *
     * @param int $dropped The number of dropped animes.
     *
     * @return void
     */
    public function setDropped($dropped)
    {
        $this->dropped = $dropped;
    }

    /**
     * Get the dropped property
     *
     * @return int
     */
    public function getDropped()
    {
       return $this->dropped;
    }

    /**
     * Set the planToWatch property
     *
     * @param int $id The number of planned to watched animes.
     *
     * @return void
     */
    public function setPlanToWatch($planToWatch)
    {
        $this->planToWatch = $planToWatch;
    }

    /**
     * Get the planToWatch property
     *
     * @return int
     */
    public function getPlanToWatch()
    {
       return $this->planToWatch;
    }

    /**
     * Set the totalEntries property
     *
     * @param int $totalEntries The total number of series.
     *
     * @return void
     */
    public function setTotalEntries($totalEntries)
    {
        $this->totalEntries = $totalEntries;
    }

    /**
     * Get the totalEntries property
     *
     * @return int
     */
    public function getTotalEntries()
    {
       return $this->totalEntries;
    }
}

/**
* This class is used within the Profile Class.
* It contains statistical information on the user's manga reading.
*/
class MangaStats
{
    private $timeDays;
    private $reading;
    private $completed;
    private $onHold;
    private $dropped;
    private $planToRead;
    private $totalEntries;

    /**
     * Set the timeDays property
     *
     * @param float $timeDays The time of days of profiles.
     *
     * @return void
     */
    public function setTimeDays($timeDays)
    {
        $this->timeDays = $timeDays;
    }

    /**
     * Get the timeDays property
     *
     * @return float
     */
    public function getTimeDays()
    {
       return $this->timeDays;
    }

    /**
     * Set the reading property
     *
     * @param int $reading The number of reading mangas.
     *
     * @return void
     */
    public function setReading($reading)
    {
        $this->reading = $reading;
    }

    /**
     * Get the reading property
     *
     * @return int
     */
    public function getReading()
    {
       return $this->reading;
    }

    /**
     * Set the completed property
     *
     * @param int $completed The number of completed mangas.
     *
     * @return void
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;
    }

    /**
     * Get the completed property
     *
     * @return int
     */
    public function getCompleted()
    {
       return $this->completed;
    }

    /**
     * Set the onHold property
     *
     * @param int $onHold The number of onHold mangas.
     *
     * @return void
     */
    public function setOnHold($onHold)
    {
        $this->onHold = $onHold;
    }

    /**
     * Get the onHold property
     *
     * @return int
     */
    public function getOnHold()
    {
       return $this->onHold;
    }

    /**
     * Set the dropped property
     *
     * @param int $dropped The number of dropped mangas.
     *
     * @return void
     */
    public function setDropped($dropped)
    {
        $this->dropped = $dropped;
    }

    /**
     * Get the dropped property
     *
     * @return int
     */
    public function getDropped()
    {
       return $this->dropped;
    }

    /**
     * Set the planToRead property
     *
     * @param int $id The number of planned to read mangas.
     *
     * @return void
     */
    public function setPlanToRead($planToRead)
    {
        $this->planToRead = $planToRead;
    }

    /**
     * Get the planToRead property
     *
     * @return int
     */
    public function getPlanToRead()
    {
       return $this->planToRead;
    }

    /**
     * Set the totalEntries property
     *
     * @param int $totalEntries The total number of series.
     *
     * @return void
     */
    public function setTotalEntries($totalEntries)
    {
        $this->totalEntries = $totalEntries;
    }

    /**
     * Get the totalEntries property
     *
     * @return int
     */
    public function getTotalEntries()
    {
       return $this->totalEntries;
    }

}
