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

class Review
{
    private $date; //The creation date.
    private $rating; //The anime rating.
    private $username; //The username who created this topic.
    private $episodes; //number of episodes.
    private $watchedEpisodes; //number of watched episodes
    private $chapters; //number of chapters.
    private $chapters_read; //Number of chapters already read.
    private $helpful; //description (board).
    private $helpfultotal; //description (board).
    private $avatar_url; //the avatar url.
    private $review; //last reply info (topic).

    /**
     * Set the date property
     *
     * @param string $date The creation date of this review
     *
     * @return void
     */
    public function setDate($date)
    {
        $this->date = $date;
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
     * Set the rating property
     *
     * @param int $rating The rating of the writer
     *
     * @return void
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
     * Set the username property
     *
     * @param string $username The username of the creator.
     *
     * @return void
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
     * Set the episodes property
     *
     * @param int $episodes The number of watched episodes
     *
     * @return void
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
     * Set the watched_episodes property
     *
     * @param string $watched_episodes The number of watched episodes.
     *
     * @return void
     */
    public function setWatchedEpisodes($watched_episodes)
    {
        $this->watchedEpisodes = (int) $watched_episodes;
    }

    /**
     * Get the watched_episodes property
     *
     * @return int
     */
    public function getWatchedEpisodes()
    {
        return $this->watchedEpisodes;
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
        $this->chapters = (int) $chapters;
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
     * Set the chapters_read property
     *
     * @param string chapters_read The number of read chapters.
     *
     * @return void
     */
    public function setChaptersRead($chapters_read)
    {
        $this->chapters_read = (int) $chapters_read;
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
     * Set the helpful property
     *
     * @param int $helpful The number of people who found it helpful
     *
     * @return void
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
     * Set the helpfultotal property
     *
     * @param int $helpfultotal Number of helpful & not helpful
     *
     * @return void
     */
    public function setHelpfulTotal($helpfultotal)
    {
        $this->helpfultotal = (int) $helpfultotal;
    }

    /**
     * Get the helpfultotal property.
     *
     * @return int
     */
    public function getHelpfulTotal()
    {
       return $this->helpfultotal;
    }

    /**
     * Set the review property
     *
     * @param string $review The review content.
     *
     * @return void
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
     * Set the review property
     *
     * @param string $avatar_url The user avatar.
     *
     * @return void
     */
    public function setAvatarUrl($avatar_url)
    {
        $this->avatar_url = $avatar_url;
    }

    /**
     * Get the avatar_url property.
     *
     * @return string
     */
    public function getAvatarUrl()
    {
        return $this->avatar_url;
    }

}