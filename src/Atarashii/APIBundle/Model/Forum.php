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

class Forum
{
    /**
     * The ID used to get topic|board.
     *
     * @Since("2.0")
     */
    private $id;

    /**
     * The forum board|topic name.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $name;

    /**
     * The username of the topic creator.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $username;

    /**
     * The number of replies of a topic.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $replies;

    /**
     * The description of a board.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $description;

    /**
     * The info of the last reply inside a topic.
     *
     * @Type("array")
     * @Since("2.0")
     */
    private $reply;

    /**
     * The children of a forumboard.
     *
     * @Type("array")
     * @Since("2.0")
     */
    private $children;

    /**
     * The comment content in an post.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $comment;

    /**
     * The creation time of this post.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $time;

    /**
     * The userprofile for the user details in topics.
     *
     * @Since("2.0")
     */
    public $profile;

    /**
     * Set the id property.
     *
     * @param int $id The id of forum board.
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

    /**
     * Get the id property.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the name property.
     *
     * @param int $name The name of the forum board.
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the name property.
     *
     * @return int
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the username property.
     *
     * @param string $username The username of the topic.
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
     * Set the replies property.
     *
     * @param int $replies The replies of the topic.
     */
    public function setReplies($replies)
    {
        $this->replies = (int) $replies;
    }

    /**
     * Get the replies property.
     *
     * @return int
     */
    public function getReplies()
    {
        return $this->replies;
    }

    /**
     * Set the description property.
     *
     * @param string $description The description of the forum board.
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get the description property.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the reply property.
     *
     * @param array $reply The last reply details.
     */
    public function setReply($reply)
    {
        $this->reply = $reply;
    }

    /**
     * Get the reply property.
     *
     * @return array
     */
    public function getReply()
    {
        return $this->reply;
    }

    /**
     * Set the children property.
     *
     * @param array $children The children of the forum board.
     */
    public function setChildren($children)
    {
        $this->children[] = $children;
    }

    /**
     * Get the children property.
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set the comment property.
     *
     * @param string $comment The comment of the reply.
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the comment property.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set the time property.
     *
     * @param string $time The creation time of the reply.
     */
    public function setTime($time)
    {
        $this->time = Date::formatTime($time);
    }

    /**
     * Get the time property.
     *
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }
}
