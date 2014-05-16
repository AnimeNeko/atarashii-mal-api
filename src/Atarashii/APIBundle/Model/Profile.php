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
    private $avatar_url;

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
     * Set the avatar_url property
     *
     * @param string $avatar_url The avatar url of an user.
     *
     * @return void
     */
    public function setAvatarUrl($avatar_url)
    {
        $this->avatar_url = $avatar_url;
    }

    /**
     * Get the avatar_url property
     *
     * @return string
     */
    public function getAvatarUrl()
    {
       return $this->avatar_url;
    }

}

/**
* This class is used within the Profile Class.
* It contains general user details
*/
class ProfileDetails
{
    private $last_online;
    private $status;
    private $gender;
    private $birthday;
    private $location;
    private $website;
    private $join_date;
    private $access_rank;
    private $anime_list_views = 0;
    private $manga_list_views = 0;
    private $forum_posts = 0;
    private $aim;
    private $comments = 0;
    private $msn;
    private $yahoo;

    /**
     * Set the last_online property
     *
     * @param string $last_online The last time when an user was online.
     *
     * @return void
     */
    public function setLastOnline($last_online)
    {
        $this->last_online = $last_online;
    }

    /**
     * Get the last_online property
     *
     * @return string
     */
    public function getLastOnline()
    {
       return $this->last_online;
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
     * Set the join_date property
     *
     * @param string $join_date The MAL join date of an user.
     *
     * @return void
     */
    public function setJoinDate($join_date)
    {
        $this->join_date = $join_date;
    }

    /**
     * Get the join_date property
     *
     * @return string
     */
    public function getJoinDate()
    {
       return $this->join_date;
    }

    /**
     * Set the access_rank property
     *
     * @param string $access_rank The MAL access rank of an user.
     *
     * @return void
     */
    public function setAccessRank($access_rank)
    {
        $this->access_rank = $access_rank;
    }

    /**
     * Get the access_rank property
     *
     * @return string
     */
    public function getAccessRank()
    {
       return $this->access_rank;
    }

    /**
     * Set the anime_list_views property
     *
     * @param int $anime_list_views The animelist views of an user.
     *
     * @return void
     */
    public function setAnimeListViews($anime_list_views)
    {
        $this->anime_list_views = $anime_list_views;
    }

    /**
     * Get the anime_list_views property
     *
     * @return int
     */
    public function getAnimeListViews()
    {
       return $this->anime_list_views;
    }

    /**
     * Set the manga_list_views property
     *
     * @param int $manga_list_views The mangalist views of an user.
     *
     * @return void
     */
    public function setMangaListViews($manga_list_views)
    {
        $this->manga_list_views = $manga_list_views;
    }

    /**
     * Get the manga_list_views property
     *
     * @return int
     */
    public function getMangaListViews()
    {
       return $this->manga_list_views;
    }

    /**
     * Set the forum_posts property
     *
     * @param int $forum_posts The number forum posts of an user.
     *
     * @return void
     */
    public function setForumPosts($forum_posts)
    {
        $this->forum_posts = $forum_posts;
    }

    /**
     * Get the forum_posts property
     *
     * @return int
     */
    public function getForumPosts()
    {
       return $this->forum_posts;
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
    private $time_days;
    private $watching;
    private $completed;
    private $on_hold;
    private $dropped;
    private $plan_to_watch;
    private $total_entries;

    /**
     * Set the time_days property
     *
     * @param float $time_days The time of days of profiles.
     *
     * @return void
     */
    public function setTimeDays($time_days)
    {
        $this->time_days = $time_days;
    }

    /**
     * Get the time_days property
     *
     * @return float
     */
    public function getTimeDays()
    {
       return $this->time_days;
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
     * Set the on_hold property
     *
     * @param int $on_hold The number of on_hold animes.
     *
     * @return void
     */
    public function setOnHold($on_hold)
    {
        $this->on_hold = $on_hold;
    }

    /**
     * Get the on_hold property
     *
     * @return int
     */
    public function getOnHold()
    {
       return $this->on_hold;
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
     * Set the plan_to_Watch property
     *
     * @param int $id The number of planned to watched animes.
     *
     * @return void
     */
    public function setPlanToWatch($plan_to_watch)
    {
        $this->plan_to_watch = $plan_to_watch;
    }

    /**
     * Get the plan_to_Watch property
     *
     * @return int
     */
    public function getPlanToWatch()
    {
       return $this->plan_to_watch;
    }

    /**
     * Set the total_entries property
     *
     * @param int $total_entries The total number of series.
     *
     * @return void
     */
    public function setTotalEntries($total_entries)
    {
        $this->total_entries = $total_entries;
    }

    /**
     * Get the total_entries property
     *
     * @return int
     */
    public function getTotalEntries()
    {
       return $this->total_entries;
    }
}

/**
* This class is used within the Profile Class.
* It contains statistical information on the user's manga reading.
*/
class MangaStats
{
    private $time_days;
    private $reading;
    private $completed;
    private $on_hold;
    private $dropped;
    private $plan_to_read;
    private $total_entries;

    /**
     * Set the time_days property
     *
     * @param float $time_days The time of days of profiles.
     *
     * @return void
     */
    public function setTimeDays($time_days)
    {
        $this->time_days = $time_days;
    }

    /**
     * Get the time_days property
     *
     * @return float
     */
    public function getTimeDays()
    {
       return $this->time_days;
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
     * Set the on_hold property
     *
     * @param int $on_hold The number of on_hold mangas.
     *
     * @return void
     */
    public function setOnHold($on_hold)
    {
        $this->on_hold = $on_hold;
    }

    /**
     * Get the on_hold property
     *
     * @return int
     */
    public function getOnHold()
    {
       return $this->on_hold;
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
     * Set the plan_to_read property
     *
     * @param int $id The number of planned to read mangas.
     *
     * @return void
     */
    public function setPlanToRead($plan_to_read)
    {
        $this->plan_to_read = $plan_to_read;
    }

    /**
     * Get the plan_to_read property
     *
     * @return int
     */
    public function getPlanToRead()
    {
       return $this->plan_to_read;
    }

    /**
     * Set the total_entries property
     *
     * @param int $total_entries The total number of series.
     *
     * @return void
     */
    public function setTotalEntries($total_entries)
    {
        $this->total_entries = $total_entries;
    }

    /**
     * Get the total_entries property
     *
     * @return int
     */
    public function getTotalEntries()
    {
       return $this->total_entries;
    }

}
