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

class Messages
{
    /**
     * The message ID used to send messages.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $id;

    /**
     * /The action ID used to perform actions like delete.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $actionId;

    /**
     * The tread ID used to view|send replies.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $threadId;

    /**
     * The username of the sender.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $username;

    /**
     * The time when the message was send.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $time;

    /**
     * The read status used to determine if you have read the content.
     *
     * @Type("boolean")
     * @Since("2.0")
     */
    private $read;

    /**
     * The subject of the received message.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $subject;

    /**
     * The message content.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $message;

    /**
     * The preview of a message.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $preview;

    /**
     * Set the id property.
     *
     * @param int $id The id of messages.
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Set the actionId property.
     *
     * @param int $actionId The action ID of messages.
     */
    public function setActionId($actionId)
    {
        $this->actionId = $actionId;
    }

    /**
     * Get the actionId property.
     *
     * @return int
     */
    public function getActionId()
    {
        return $this->actionId;
    }

    /**
     * Set the threadId property.
     *
     * @param int $threadId The thread ID of messages.
     */
    public function setThreadId($threadId)
    {
        $this->threadId = $threadId;
    }

    /**
     * Get the threadId property.
     *
     * @return int
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * Set the username property.
     *
     * @param string $username The username of messages.
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
     * Set the time property.
     *
     * @param DateTime $time The time of messages.
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

    /**
     * Set the read property.
     *
     * @param bool $read The read status of messages.
     */
    public function setRead($read)
    {
        $this->read = $read;
    }

    /**
     * Get the read property.
     *
     * @return bool
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * Set the subject property.
     *
     * @param string $subject The subject of messages.
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Get the subject property.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the message property.
     *
     * @param string $message The message of messages.
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Get the message property.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the preview property.
     *
     * @param string $preview The preview of messages.
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;
    }

    /**
     * Get the preview property.
     *
     * @return string
     */
    public function getPreview()
    {
        return $this->preview;
    }
}
