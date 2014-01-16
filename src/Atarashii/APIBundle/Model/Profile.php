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

class Profile
{
    public $avatar_url; //URL to user's avatar (This should be under details, not out here, but Ruby API does it this way)
    public $details = array(); //user's general (not anime/manga-specific) details.
    public $anime_stats = array(); //user's anime statistics.
    public $manga_stats = array(); //user's manga statistics.

    public function __construct()
    {
        $this->details = new ProfileDetails();
    }

}

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
