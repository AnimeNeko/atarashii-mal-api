<?php

namespace Atarashii\APIBundle\Tests\Model;

use Atarashii\APIBundle\Model\Messages;

class MessagesTest extends \PHPUnit_Framework_TestCase
{
    public function testId()
    {
        $msgId = rand();

        $message = new Messages();
        $message->setId($msgId);

        $this->assertEquals($msgId, $message->getId());
    }

    public function testActionId()
    {
        $actionId = rand();

        $message = new Messages();
        $message->setActionId($actionId);

        $this->assertEquals($actionId, $message->getActionId());
    }

    public function testThreadId()
    {
        $actionId = rand();

        $message = new Messages();
        $message->setThreadId($actionId);

        $this->assertEquals($actionId, $message->getThreadId());
    }

    public function testUsername()
    {
        $msgUsername = 'TaroYamada';

        $message = new Messages();
        $message->setUsername($msgUsername);

        $this->assertEquals($msgUsername, $message->getUsername());
    }

    public function testTime()
    {
        $msgTime = 'May 17, 2014 2:23 AM';
        $expected = '2014-05-17T02:23-0700';

        $message = new Messages();
        $message->setTime($msgTime);

        $this->assertEquals($expected, $message->getTime());
    }

    public function testRead()
    {
        $readStatus = true;

        $message = new Messages();
        $message->setRead($readStatus);

        $this->assertEquals($readStatus, $message->getRead());
    }

    public function testSubject()
    {
        $subject = 'This is a subject.';

        $message = new Messages();
        $message->setSubject($subject);

        $this->assertEquals($subject, $message->getSubject());
    }

    public function testMessage()
    {
        $msgText = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean molestie nisi sed lacus tincidunt, vel vestibulum enim facilisis. Nunc consectetur eget justo placerat scelerisque. Nullam eu augue ullamcorper, faucibus elit sed, malesuada libero. Proin pretium elit quis arcu eleifend venenatis. Vestibulum efficitur cursus tellus eu pharetra. Cras pharetra accumsan consequat.';

        $message = new Messages();
        $message->setMessage($msgText);

        $this->assertEquals($msgText, $message->getMessage());
    }

    public function testPreview()
    {
        $msgText = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit...';

        $message = new Messages();
        $message->setPreview($msgText);

        $this->assertEquals($msgText, $message->getPreview());
    }
}
