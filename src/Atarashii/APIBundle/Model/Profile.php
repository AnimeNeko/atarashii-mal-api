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
    public $avatar_url;

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

}

/**
* This class is used within the Profile Class.
* It contains general user details
*/
class ProfileDetails
{
    public $last_online;
    public $gender;
    public $birthday;
    public $location;
    public $website;
    public $join_date;
    public $access_rank;
    public $anime_list_views = 0;
    public $manga_list_views = 0;
    public $forum_posts = 0;
    public $aim;
    public $comments = 0;
    public $msn;
    public $yahoo;
}

/**
* This class is used within the Profile Class.
* It contains statistical information on the user's anime watching.
*/
class AnimeStats
{
    public $time_days;
    public $watching;
    public $completed;
    public $on_hold;
    public $dropped;
    public $plan_to_watch;
    public $total_entries;
}

/**
* This class is used within the Profile Class.
* It contains statistical information on the user's manga reading.
*/
class MangaStats
{
    public $time_days;
    public $reading;
    public $completed;
    public $on_hold;
    public $dropped;
    public $plan_to_read;
    public $total_entries;
}