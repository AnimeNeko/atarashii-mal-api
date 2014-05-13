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

class Messages
{
    private $id; //The message ID used to send messages.
    private $action_id; //The action ID (used to perform actions like delete).
    private $thread_id; //The tread ID (used to view/send replies).
    private $username; //username who send the message.
    private $time; //The time when we received the message.
    private $read; //The read status (true if you saw the message).
    private $subject; //The subject of a message.
    private $message; //The message.
    private $preview; //The preview message.


    /**
     * Set the id property
     *
     * @param int $id The id of messages.
     *
     * @return void
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
     * Set the action_id property
     *
     * @param int $action_id The action ID of messages.
     *
     * @return void
     */
    public function setActionId($action_id)
    {
        $this->action_id = $action_id;
    }

    /**
     * Get the action_id property.
     *
     * @return int
     */
    public function getActionId()
    {
       return $this->action_id;
    }

    /**
     * Set the thread_id property
     *
     * @param int $thread_id The thread ID of messages.
     *
     * @return void
     */
    public function setThreadId($thread_id)
    {
        $this->thread_id = $thread_id;
    }

    /**
     * Get the thread_id property.
     *
     * @return int
     */
    public function getThreadId()
    {
       return $this->thread_id;
    }

    /**
     * Set the username property
     *
     * @param string $username The username of messages.
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
     * Set the time property
     *
     * @param string $time The time of messages.
     *
     * @return void
     */
    public function setTime($time)
    {
        $this->time = $time;
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
     * Set the read property
     *
     * @param boolean $read The read status of messages.
     *
     * @return void
     */
    public function setRead($read)
    {
        $this->read = $read;
    }

    /**
     * Get the read property.
     *
     * @return boolean
     */
    public function getRead()
    {
       return $this->read;
    }

    /**
     * Set the subject property
     *
     * @param string $subject The subject of messages.
     *
     * @return void
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
     * Set the message property
     *
     * @param string $message The message of messages.
     *
     * @return void
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
     * Set the preview property
     *
     * @param string $preview The preview of messages.
     *
     * @return void
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
