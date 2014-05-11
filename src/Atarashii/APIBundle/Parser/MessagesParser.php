<?php
/**
* Atarashii MAL API
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/

namespace Atarashii\APIBundle\Parser;

use Symfony\Component\DomCrawler\Crawler;
use Atarashii\APIBundle\Model\Messages;

class MessagesParser
{
    public static function parse($contents)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        //Unread messages
        $messagesitems = $crawler->filter('div[class="message row_highlighted clearfix"]');
        foreach ($messagesitems as $item) {
            $resultset[] = self::parseListview($item, false);
        }

        //Read messages
        $messagesitems = $crawler->filter('div[class="message row_not_highlighted clearfix"]');
        foreach ($messagesitems as $item) {
            $resultset[] = self::parseListview($item, true);
        }

        return $resultset;
    }

    private static function parseListview($item, $read)
    {
        $crawler = new Crawler($item);
        $message = new Messages();

        # Message id.
        # Example:
        # <input type="checkbox" id="msgchecker00000000" name="msg[]" value="0-0000000">
        $message->id = (int) str_replace('?go=read&id=', '', $crawler->filter('div[class="mym_subject"] a')->attr('href'));

        # Action id.
        # Example:
        # <input type="checkbox" id="msgchecker00000000" name="msg[]" value="0-0000000">
        $message->action_id = (int) str_replace('msgchecker', '', $crawler->filter('div[class="mym_checkboxes"] input')->attr('id'));

        # Username of the sender.
        # Example:
        # <a href="http://myanimelist.net/profile/ratan12">ratan12</a>
        $message->username = $crawler->filter('div[class="mym_user"] a')->text();

        # Thread id.
        # Example:
        # <a href="?go=send&replyid=00000000&threadid=00000000&toname=xxxx">Reply</a>
        $message->thread_id = (int) str_replace('&toname='.$message->username,'',str_replace('?go=send&replyid='.$message->action_id.'&threadid=', '', $crawler->filter('div[class="mym_actions"] a')->attr('href')));

        # Time of the received message.
        # Example:
        # <small>50 minutes ago</small>
        $message->time = $crawler->filter('div[class="mym_user"] small')->text();

        # Read (if the user has read this message).
        $message->read = $read;

        # Subject.
        # Example:
        # <div style="margin-bottom: 4px; font-weight: bold;">Subject example</div>
        $message->subject = $crawler->filter('div[class="mym_subject"] > a')->text();

        # Preview message.
        # Example:
        # <a href="?go=read&id=5665695" class="lightLink">this is the first rule of an awesome message!</a>
        $message->preview = $crawler->filter('div[class="mym_subject"] a[class="lightLink"]')->text();

        return $message;
    }

    public static function parseMessage($contents, $id)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent(str_replace('<br />','',$contents), 'UTF-8');

        $message = new Messages();

        $message->id = (int) $id;

        # Action id of the message.
        # Example:
        # <input type="button" onclick="document.location='/mymessages.php?go=send&replyid=21193061&threadid=16092543&toname=Ratan12'" class="inputButton" value=" Reply ">
        $del_button = "window.location='/mymessages.php?go=delete&id=";
        $del_button = str_replace($del_button, '', $crawler->filter('td div input')->eq(2)->attr('onclick'));
        $message->action_id = (int) str_replace("'","",$del_button);


        # Thread id of the message.
        # Example:
        # <a href="?go=read&id=0000000&threadid=00000000">
        $message->thread_id = (int) str_replace('?go=read&id='.$id.'&threadid=','',$crawler->filter('div div div div a')->attr('href'));

        # Username of the sender.
        # Example:
        # <a href="http://myanimelist.net/profile/ratan12">ratan12</a>
        $message->username = $crawler->filter('h2 a')->text();

        # Time of the received message.
        # Example:
        # <small>50 minutes ago</small>
        $message->time = $crawler->filter('div small')->text();

        # Subject.
        # Example:
        # <div style="margin-bottom: 4px; font-weight: bold;">re: coolmessage</div>
        $message->subject = $crawler->filter('td div')->eq(1)->text();

        # Message.
        $message->message = str_replace('Message Sent from '.$message->username.$message->time.$message->subject, '', $crawler->filter('tr td')->text());

        return $message;
    }
}
