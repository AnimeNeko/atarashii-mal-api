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
    public $id; //The message ID used to send messages.
    public $action_id; //The action ID (used to perform actions like delete).
    public $thread_id; //The tread ID (used to view/send replies).
    public $username; //username who send the message.
    public $time; //The time when we received the message.
    public $read; //The read status (true if you saw the message).
    public $subject; //The subject of a message.
    public $message; //The message.
    public $preview; //The preview message.
}
