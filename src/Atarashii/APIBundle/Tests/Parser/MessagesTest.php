<?php

namespace Atarashii\APIBundle\Tests\Messages;

use Atarashii\APIBundle\Parser\Messages;
use Atarashii\APIBundle\Parser\MessagesParser;

class MessagesTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $messageIndex = file_get_contents(__DIR__ . '/../InputSamples/user-messages-list.html');

        $messages = MessagesParser::parse($messageIndex);

        $this->assertInternalType('array', $messages);
        $this->assertInternalType('int', $messages['pages']);
        $this->assertInternalType('array', $messages['list']);

        $message = $messages['list'][0];

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Messages', $message);

        //Do some basic type checking for things that should exist (not null)
        $this->assertInternalType('int', $message->getId());
        $this->assertInternalType('int', $message->getActionId());
        $this->assertInternalType('int', $message->getThreadId());
        $this->assertInternalType('string', $message->getUsername());
        $this->assertInternalType('bool', $message->getRead());
        $this->assertInternalType('string', $message->getSubject());
        $this->assertNull($message->getMessage());
        $this->assertInternalType('string', $message->getPreview());
    }

    public function testParseMessage()
    {
        $messageContent = file_get_contents(__DIR__.'/../InputSamples/user-message.html');

        $message = MessagesParser::parseMessage($messageContent, 1234);

        $this->assertInstanceOf('Atarashii\APIBundle\Model\Messages', $message);

        //Do some basic type checking for things that should exist (not null)
        $this->assertInternalType('int', $message->getId());
        $this->assertInternalType('string', $message->getUsername());
        $this->assertInternalType('string', $message->getSubject());
        $this->assertInternalType('string', $message->getMessage());
        $this->assertNull($message->getPreview());
    }
}
