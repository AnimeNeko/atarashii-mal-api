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

use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use Atarashii\APIBundle\Helper\Date;

class Review
{
    /**
     * The creation date of the review.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $date;

    /**
     * The rating given by the user.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $rating;

    /**
     * The username of the review creator.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $username;

    /**
     * The the number of the max episodes.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $episodes;

    /**
     * The number of watched episodes of the review creator.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $watchedEpisodes;

    /**
     * The number of the max chapters.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $chapters;

    /**
     * The number of read chapters of the review creator.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $chaptersRead;

    /**
     * The number of users who marked this review helpful.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $helpful;

    /**
     * The number of users who voted for helpful & not helpful.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $helpfulTotal;

    /**
     * The avatar URL of the review creator.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $avatarUrl;

    /**
     * The review content.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $review;

    /**
     * Set the date property.
     *
     * @param string $date The creation date of this review
     */
    public function setDate($date)
    {
        $this->date = Date::formatTime($date);
    }

    /**
     * Get the date property.
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the rating property.
     *
     * @param int $rating The rating of the writer
     */
    public function setRating($rating)
    {
        $this->rating = (int) $rating;
    }

    /**
     * Get the rating property.
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set the username property.
     *
     * @param string $username The username of the creator.
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get the username property.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the episodes property.
     *
     * @param int $episodes The number of watched episodes
     */
    public function setEpisodes($episodes)
    {
        $this->episodes = (int) $episodes;
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
     * Set the watched_episodes property.
     *
     * @param string $watched_episodes The number of watched episodes.
     */
    public function setWatchedEpisodes($watched_episodes)
    {
        $this->watchedEpisodes = (int) $watched_episodes;
    }

    /**
     * Get the watched_episodes property.
     *
     * @return int
     */
    public function getWatchedEpisodes()
    {
        return $this->watchedEpisodes;
    }

    /**
     * Set the chapters property.
     *
     * @param int $chapters The number chapters of series.
     */
    public function setChapters($chapters)
    {
        $this->chapters = (int) $chapters;
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
     * Set the chaptersRead property.
     *
     * @param string chaptersRead The number of read chapters.
     */
    public function setChaptersRead($chaptersRead)
    {
        $this->chaptersRead = (int) $chaptersRead;
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
     * Set the helpful property.
     *
     * @param int $helpful The number of people who found it helpful
     */
    public function setHelpful($helpful)
    {
        $this->helpful = (int) $helpful;
    }

    /**
     * Get the helpful property.
     *
     * @return int
     */
    public function getHelpful()
    {
        return $this->helpful;
    }

    /**
     * Set the helpfulTotal property.
     *
     * @param int $helpfulTotal Number of helpful & not helpful
     */
    public function setHelpfulTotal($helpfulTotal)
    {
        $this->helpfulTotal = (int) $helpfulTotal;
    }

    /**
     * Get the helpfulTotal property.
     *
     * @return int
     */
    public function getHelpfulTotal()
    {
        return $this->helpfulTotal;
    }

    /**
     * Set the review property.
     *
     * @param string $review The review content.
     */
    public function setReview($review)
    {
        $this->review = $review;
    }

    /**
     * Get the review property.
     *
     * @return string
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set the review property.
     *
     * @param string $avatarUrl The user avatar.
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
