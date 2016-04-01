<?php
/**
* Atarashii MAL API.
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014-2015 Ratan Dhawtal and Michael Johnson
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
        $resultset = array();

        //Message container
        $messageList = $crawler->filterXPath('//div[@class="message-container"]//div[contains(@class,"message")]');

        foreach ($messageList as $message) {
            $isRead = strstr($message->getAttribute("class"), 'unread') ? false : true;
            $resultset[] = self::parseListview($message, $isRead);
        }

        $pages = $crawler->filterXPath('//form[@name="messageForm"]/div/div[contains(@class,"fl-r")]');

        if (count($pages) > 0) {
            if (preg_match('/Pages \((.*?)\)/', $pages->html(), $numPages) !== false) {
                $result['pages'] = (int) $numPages[1];
            }
        } else {
            $result['pages'] = 1;
        }

        $result['list'] = $resultset;

        return $result;
    }

    private static function parseListview($item, $read)
    {
        $crawler = new Crawler($item);
        $message = new Messages();

        # Message id.
        $message->setId((int) str_replace('?go=read&id=', '', $crawler->filterXPath('//div[contains(@class, "mym_subject")]/a')->attr('href')));

        # Action id.
        $message->setActionId((int) str_replace('msgchecker', '', $crawler->filterXPath('//div[contains(@class, "mym_checkboxes")]/input')->attr('id')));

        # Thread id.
        $message->setThreadId((int) str_replace('&toname='.$message->getUsername(), '', str_replace('?go=send&replyid='.$message->GetActionId().'&threadid=', '', $crawler->filterXPath('//span[contains(@class, "mym_actions")]/a')->attr('href'))));

        # Username of the sender.
        $message->setUsername($crawler->filterXPath('//div[contains(@class, "mym_user")]/a')->text());

        # Time of the received message.
        $message->setTime($crawler->filterXPath('//span[@class="mym_date"]')->text());

        # Read (if the user has read this message).
        $message->setRead($read);

        # Subject and Preview are linked together
        $messagePreview = trim($crawler->filterXPath('//div[contains(@class, "mym_subject")]/a/span')->text());
        $messageSubject = trim(str_replace($messagePreview, '', $crawler->filterXPath('//div[contains(@class, "mym_subject")]/a')->text()));
        $messageSubject = preg_replace('/ -$/', '', $messageSubject);

        # Subject.
        $message->setSubject($messageSubject);

        # Preview message.
        $message->setPreview($messagePreview);

        return $message;
    }

    public static function parseMessage($contents, $id)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent(str_replace('<br />', '', $contents), 'UTF-8');

        $message = new Messages();

        $message->setId((int) $id);

        # Action id of the message.
        # Example:
        # <input type="button" onclick="document.location='/mymessages.php?go=send&replyid=21193061&threadid=16092543&toname=Ratan12'" class="inputButton" value=" Reply ">
        $actionId = $crawler->filterXPath('//form[contains(@action,"delete")]/input[@name="id"]');
        $message->setActionId((int) $actionId->attr('value'));

        # Thread id of the message.
        # Example:
        # <a href="?go=read&id=0000000&threadid=00000000">
        $threadId = $crawler->filterXPath('//div/input[contains(@value,"Reply")]');
        $threadId = $threadId->attr('onclick');

        if (preg_match('/threadid=([\d]*)/', $threadId, $threadMatches)) {
            $message->setThreadId((int) $threadMatches[1]);
        }

        # Username of the sender.
        # Example:
        # <a href="http://myanimelist.net/profile/ratan12">ratan12</a>
        $message->setUsername($crawler->filterXPath('//td[@class="dialog-text"]/h2/a')->text());

        # Time of the received message.
        # Example:
        # <small>50 minutes ago</small>
        $time = $crawler->filterXPath('//td[@class="dialog-text"]/div[contains(@class,"lightLink")]');

        if (count($time) > 0) {
            $message->setTime($time->text());
        }

        # Subject.
        # Example:
        # <div style="margin-bottom: 4px; font-weight: bold;">re: coolmessage</div>
        $messageSubject = $crawler->filterXPath('//td[@class="dialog-text"]/div[contains(@class,"fw-b")]')->text();
        $message->setSubject($messageSubject);

        # Message.
        $messageText = $crawler->filterXPath('//td[@class="dialog-text"]');

        if (preg_match('/Test Test<\/div>(.*?)<div/s', $messageText->html(), $messageBody)) {
            $message->setMessage($messageBody[1]);
        }

        return $message;
    }
}
