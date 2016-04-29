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
use Atarashii\APIBundle\Helper\Date;

/**
 * An instance of this class represents a single user's profile.
 */
class Profile
{
    /**
     * Fully qualified URL to the user's avatar image.
     *
     * @var string
     */
    private $avatarUrl;

    /**
     * A ProfileDetails object containing general information on the user.
     *
     * @var ProfileDetails
     */
    public $details;

    /**
     * An AnimeStats object containing information on the user's anime statistics.
     *
     * @var AnimeStats
     */
    public $anime_stats;

    /**
     * A MangaStats object containing information on the user's manga statistics.
     *
     * @var MangaStats
     */
    public $manga_stats;

    /**
     * Create an instance of the object.
     */
    public function __construct()
    {
        // Initialize the sub classes we use.
        $this->details = new ProfileDetails();
        $this->anime_stats = new AnimeStats();
        $this->manga_stats = new MangaStats();
    }

    /**
     * Set the avatarUrl property.
     *
     * @param string $avatarUrl The avatar url of an user.
     */
    public function setAvatarUrl($avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;
    }

    /**
     * Get the avatarUrl property.
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
 * It contains general user details.
 */
class ProfileDetails
{
    /**
     * The date of when the user was last online.
     *
     * API 1.0
     * Formatted for compatibility with the old Ruby API.
     * It is not formatted, this means it would return raw time information from MAL.
     * The value is null if the date is unknown.
     * Example: "12-14-14, 12:01 PM" or "16 minutes ago".
     *
     * @Type("string")
     * @Until("2.0")
     */
    private $lastOnline;

    /**
     * The date of when the user was last online.
     *
     * API 2.0+
     * Formatted as an ISO8601-compatible string.
     * The contents my be formatted as a year, year and month, or year, month and day.
     * The value is null if the date is unknown.
     * Example: "2004-10-07T10:05+0100", "2004-10-07T10+0100", or "2004-10-07+0100".
     *
     * @SerializedName("last_online")
     * @Type("string")
     * @Since("2.0")
     */
    private $lastOnline2;

    /**
     * The status of an user.
     *
     * This will indicate if an user is online or offline.
     * Example: "Online" or "Offline".
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $status;

    private $gender;
    private $birthday;
    private $location;
    private $website;

    /**
     * The date of when the user joined MAL.
     *
     * API 1.0
     * Formatted for compatibility with the old Ruby API.
     * It is not formatted, this means it would return raw time information from MAL.
     * The value is null if the date is unknown.
     * Example: "12-14-14, 12:01 PM" or "16 minutes ago".
     *
     * @Type("string")
     * @Until("2.0")
     */
    private $joinDate;

    /**
     * The date of when the user joined MAL.
     *
     * API 2.0+
     * Formatted as an ISO8601-compatible string.
     * The contents my be formatted as a year, year and month, or year, month and day.
     * The value is null if the date is unknown.
     * Example: "2004-10-07T10:05+0100", "2004-10-07T10+0100", or "2004-10-07+0100".
     *
     * @SerializedName("join_date")
     * @Type("string")
     * @Since("2.0")
     */
    private $joinDate2;

    private $accessRank;
    private $animeListViews = 0;
    private $mangaListViews = 0;
    private $forumPosts = 0;

    /**
     * The amount of created reviews.
     *
     * @Since("2.1")
     * @Type("integer")
     */
    private $reviews = 0;

    /**
     * The amount of created recommendations.
     *
     * @Since("2.1")
     * @Type("integer")
     */
    private $recommendations  = 0;

    /**
     * The amount of created blog posts.
     *
     * @Since("2.1")
     * @Type("integer")
     */
    private $blogPosts  = 0;

    /**
     * The amount of joined clubs.
     *
     * @Since("2.1")
     * @Type("integer")
     */
    private $clubs  = 0;

    private $aim;
    private $comments = 0;
    private $msn;
    private $yahoo;

    /**
     * Set the lastOnline property.
     *
     * This function is for compatibility with certain parts of API1
     *
     * @param string $lastOnline The last time when an user was online.
     */
    public function setLastOnline($lastOnline)
    {
        $this->lastOnline = $lastOnline;

        $this->lastOnline2 = Date::formatTime($lastOnline);
    }

    /**
     * Get the lastOnline property.
     *
     * @return string
     */
    public function getLastOnline()
    {
        return $this->lastOnline;
    }

    public function getLastOnline2()
    {
        return $this->lastOnline2;
    }

    /**
     * Set the status property.
     *
     * @param string $status The status of an user.
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
     * Set the gender property.
     *
     * @param string $gender The gender of an user.
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * Get the gender property.
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set the birthday property.
     *
     * @param string $birthday The birthday of an user.
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * Get the birthday property.
     *
     * @return string
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set the location property.
     *
     * @param string $location The location of an user.
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Get the location property.
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set the website property.
     *
     * @param string $website The website of an user.
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * Get the website property.
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set the joinDate property.
     *
     * This function is for compatibility with certain parts of API1
     *
     * @param string $joinDate The MAL join date of an user.
     */
    public function setJoinDate($joinDate)
    {
        $this->joinDate = $joinDate;

        $this->joinDate2 = Date::formatTime($joinDate);
    }

    /**
     * Get the joinDate property.
     *
     * @return string
     */
    public function getJoinDate()
    {
        return $this->joinDate;
    }

    public function getJoinDate2()
    {
        return $this->joinDate2;
    }

    /**
     * Set the accessRank property.
     *
     * @param string $accessRank The MAL access rank of an user.
     */
    public function setAccessRank($accessRank)
    {
        $this->accessRank = $accessRank;
    }

    /**
     * Get the accessRank property.
     *
     * @return string
     */
    public function getAccessRank()
    {
        return $this->accessRank;
    }

    /**
     * Set the animeListViews property.
     *
     * @param int $animeListViews The animelist views of an user.
     */
    public function setAnimeListViews($animeListViews)
    {
        $this->animeListViews = $animeListViews;
    }

    /**
     * Get the animeListViews property.
     *
     * @return int
     */
    public function getAnimeListViews()
    {
        return $this->animeListViews;
    }

    /**
     * Set the mangaListViews property.
     *
     * @param int $mangaListViews The mangalist views of an user.
     */
    public function setMangaListViews($mangaListViews)
    {
        $this->mangaListViews = $mangaListViews;
    }

    /**
     * Get the mangaListViews property.
     *
     * @return int
     */
    public function getMangaListViews()
    {
        return $this->mangaListViews;
    }

    /**
     * Set the forumPosts property.
     *
     * @param int $forumPosts The number forum posts of an user.
     */
    public function setForumPosts($forumPosts)
    {
        $this->forumPosts = $forumPosts;
    }

    /**
     * Get the forumPosts property.
     *
     * @return int
     */
    public function getForumPosts()
    {
        return $this->forumPosts;
    }

    /**
     * Set the reviews property.
     *
     * @param int $reviews The number reviews which an user created.
     */
    public function setReviews($reviews)
    {
        $this->reviews = $reviews;
    }

    /**
     * Get the reviews property.
     *
     * @return int
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Set the recommendations property.
     *
     * @param int $recommendations The number recommendations which an user created.
     */
    public function setRecommedations($recommendations)
    {
        $this->recommendations = $recommendations;
    }

    /**
     * Get the recommendations property.
     *
     * @return int
     */
    public function getRecommedations()
    {
        return $this->recommendations;
    }

    /**
     * Set the blogPosts property.
     *
     * @param int $blogPosts The number of blogPosts created by an user.
     */
    public function setBlogPosts($blogPosts)
    {
        $this->blogPosts = $blogPosts;
    }

    /**
     * Get the blogPosts property.
     *
     * @return int
     */
    public function getBlogPosts()
    {
        return $this->blogPosts;
    }

    /**
     * Set the clubs property.
     *
     * @param int $clubs The number clubs the user joined.
     */
    public function setClubs($clubs)
    {
        $this->clubs = $clubs;
    }

    /**
     * Get the clubs property.
     *
     * @return int
     */
    public function getClubs()
    {
        return $this->clubs;
    }

    /**
     * Set the aim property.
     *
     * @param string $aim The aim of an user.
     */
    public function setAim($aim)
    {
        $this->aim = $aim;
    }

    /**
     * Get the aim property.
     *
     * @return string
     */
    public function getAim()
    {
        return $this->aim;
    }

    /**
     * Set the comments property.
     *
     * @param int $comments The number comments of an user.
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * Get the comments property.
     *
     * @return int
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set the msn property.
     *
     * @param string $msn The msn of an user.
     */
    public function setMsn($msn)
    {
        $this->msn = $msn;
    }

    /**
     * Get the msn property.
     *
     * @return string
     */
    public function getMsn()
    {
        return $this->msn;
    }

    /**
     * Set the yahoo property.
     *
     * @param string $yahoo The yahoo of an user.
     */
    public function setYahoo($yahoo)
    {
        $this->yahoo = $yahoo;
    }

    /**
     * Get the yahoo property.
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

    /**
     * The mean score of all anime or manga records.
     *
     * @Since("2.1")
     * @Type("float")
     */
    private $meanScore;

    private $watching;
    private $completed;
    private $onHold;
    private $dropped;
    private $planToWatch;
    private $totalEntries;

    /**
     * The total times of rewatched records.
     *
     * @Since("2.1")
     * @Type("integer")
     */
    private $rewatched;

    /**
     * The amount of watched records.
     *
     * @Since("2.1")
     * @Type("integer")
     */
    private $episodes;

    /**
     * Set the timeDays property.
     *
     * @param float $timeDays The time of days of profiles.
     */
    public function setTimeDays($timeDays)
    {
        $this->timeDays = $timeDays;
    }

    /**
     * Get the timeDays property.
     *
     * @return float
     */
    public function getTimeDays()
    {
        return $this->timeDays;
    }

    /**
     * Set the meanScore property.
     *
     * @param float $meanScore The mean score of all the records.
     */
    public function setMeanScore($meanScore)
    {
        $this->meanScore = $meanScore;
    }

    /**
     * Get the meanScore property.
     *
     * @return float
     */
    public function getMeanScore()
    {
        return $this->meanScore;
    }

    /**
     * Set the watching property.
     *
     * @param int $reading The number of watching animes.
     */
    public function setWatching($watching)
    {
        $this->watching = $watching;
    }

    /**
     * Get the watching property.
     *
     * @return int
     */
    public function getWatching()
    {
        return $this->watching;
    }

    /**
     * Set the completed property.
     *
     * @param int $completed The number of completed animes.
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;
    }

    /**
     * Get the completed property.
     *
     * @return int
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * Set the onHold property.
     *
     * @param int $onHold The number of onHold animes.
     */
    public function setOnHold($onHold)
    {
        $this->onHold = $onHold;
    }

    /**
     * Get the onHold property.
     *
     * @return int
     */
    public function getOnHold()
    {
        return $this->onHold;
    }

    /**
     * Set the dropped property.
     *
     * @param int $dropped The number of dropped animes.
     */
    public function setDropped($dropped)
    {
        $this->dropped = $dropped;
    }

    /**
     * Get the dropped property.
     *
     * @return int
     */
    public function getDropped()
    {
        return $this->dropped;
    }

    /**
     * Set the planToWatch property.
     *
     * @param int $id The number of planned to watched animes.
     */
    public function setPlanToWatch($planToWatch)
    {
        $this->planToWatch = $planToWatch;
    }

    /**
     * Get the planToWatch property.
     *
     * @return int
     */
    public function getPlanToWatch()
    {
        return $this->planToWatch;
    }

    /**
     * Set the totalEntries property.
     *
     * @param int $totalEntries The total number of series.
     */
    public function setTotalEntries($totalEntries)
    {
        $this->totalEntries = $totalEntries;
    }

    /**
     * Get the totalEntries property.
     *
     * @return int
     */
    public function getTotalEntries()
    {
        return $this->totalEntries;
    }

    /**
     * Set the rewatched property.
     *
     * @param int $rewatched The total number of rewatched series.
     */
    public function setRewatched($rewatched)
    {
        $this->rewatched = $rewatched;
    }

    /**
     * Get the rewatched property.
     *
     * @return int
     */
    public function getRewatched()
    {
        return $this->rewatched;
    }

    /**
     * Set the episodes property.
     *
     * @param int $episodes The total of watched episodes.
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
}

/**
 * This class is used within the Profile Class.
 * It contains statistical information on the user's manga reading.
 */
class MangaStats
{
    private $timeDays;

    /**
     * The mean score of all anime or manga records.
     *
     * @Since("2.1")
     * @Type("float")
     */
    private $meanScore;

    private $reading;
    private $completed;
    private $onHold;
    private $dropped;
    private $planToRead;
    private $totalEntries;

    /**
     * The total times of reread records.
     *
     * @Since("2.1")
     * @Type("integer")
     */
    private $reread;

    /**
     * The amount of read volumes.
     *
     * @Since("2.1")
     * @Type("integer")
     */
    private $volumes;

    /**
     * Set the timeDays property.
     *
     * @param float $timeDays The time of days of profiles.
     */
    public function setTimeDays($timeDays)
    {
        $this->timeDays = $timeDays;
    }

    /**
     * Get the timeDays property.
     *
     * @return float
     */
    public function getTimeDays()
    {
        return $this->timeDays;
    }

    /**
     * Set the meanScore property.
     *
     * @param float $meanScore The mean score of all the records.
     */
    public function setMeanScore($meanScore)
    {
        $this->meanScore = $meanScore;
    }

    /**
     * Get the meanScore property.
     *
     * @return float
     */
    public function getMeanScore()
    {
        return $this->meanScore;
    }

    /**
     * Set the reading property.
     *
     * @param int $reading The number of reading mangas.
     */
    public function setReading($reading)
    {
        $this->reading = $reading;
    }

    /**
     * Get the reading property.
     *
     * @return int
     */
    public function getReading()
    {
        return $this->reading;
    }

    /**
     * Set the completed property.
     *
     * @param int $completed The number of completed mangas.
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;
    }

    /**
     * Get the completed property.
     *
     * @return int
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * Set the onHold property.
     *
     * @param int $onHold The number of onHold mangas.
     */
    public function setOnHold($onHold)
    {
        $this->onHold = $onHold;
    }

    /**
     * Get the onHold property.
     *
     * @return int
     */
    public function getOnHold()
    {
        return $this->onHold;
    }

    /**
     * Set the dropped property.
     *
     * @param int $dropped The number of dropped mangas.
     */
    public function setDropped($dropped)
    {
        $this->dropped = $dropped;
    }

    /**
     * Get the dropped property.
     *
     * @return int
     */
    public function getDropped()
    {
        return $this->dropped;
    }

    /**
     * Set the planToRead property.
     *
     * @param int $id The number of planned to read mangas.
     */
    public function setPlanToRead($planToRead)
    {
        $this->planToRead = $planToRead;
    }

    /**
     * Get the planToRead property.
     *
     * @return int
     */
    public function getPlanToRead()
    {
        return $this->planToRead;
    }

    /**
     * Set the totalEntries property.
     *
     * @param int $totalEntries The total number of series.
     */
    public function setTotalEntries($totalEntries)
    {
        $this->totalEntries = $totalEntries;
    }

    /**
     * Get the totalEntries property.
     *
     * @return int
     */
    public function getTotalEntries()
    {
        return $this->totalEntries;
    }

    /**
     * Set the reread property.
     *
     * @param int $reread The total number of reread series.
     */
    public function setReread($reread)
    {
        $this->reread = $reread;
    }

    /**
     * Get the reread property.
     *
     * @return int
     */
    public function getReread()
    {
        return $this->reread;
    }

    /**
     * Set the volumes property.
     *
     * @param int $volumes The total of read volumes.
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
}
