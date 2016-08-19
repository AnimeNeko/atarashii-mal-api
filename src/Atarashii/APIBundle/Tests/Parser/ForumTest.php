<?php

namespace Atarashii\APIBundle\Tests\Parser;

use Atarashii\APIBundle\Parser\ForumParser;

class ForumTest extends \PHPUnit_Framework_TestCase
{
    public function testParseBoard()
    {
        $forumIndexContent = file_get_contents(__DIR__.'/../InputSamples/forum-index.html');

        $forumIndex = ForumParser::parseBoard($forumIndexContent);

        $this->assertInternalType('array', $forumIndex);

        //Sections are indexed by name, not integer
        //Pick the "MyAnimeList" section, which should be safe for long-term use.
        $forumCategory = $forumIndex['MyAnimeList'];

        $this->assertInternalType('array', $forumCategory);

        foreach ($forumCategory as $forumBoard) {
            $this->assertInstanceOf('Atarashii\APIBundle\Model\Forum', $forumBoard);


            if ($forumBoard->getId() == 5) { //The main "Updates and Announcements" Board
                $board1 = $forumBoard;
            } elseif ($forumBoard->getId() === null) { //A board that has children doesn't have its own ID.
                $board2 = $forumBoard;
            }
        }

        //Do some sanity checking on the main board
        $this->assertInternalType('int', $board1->getId());
        $this->assertInternalType('string', $board1->getName());
        $this->assertInternalType('string', $board1->getDescription());

        //Some sanity checking on the board with kids
        $this->assertNull($board2->getId());
        $this->assertInternalType('string', $board2->getName());
        $this->assertInternalType('string', $board2->getDescription());
        $this->assertInternalType('array', $board2->getChildren());

        foreach ($board2->getChildren() as $boardChild) {
            $this->assertInstanceOf('Atarashii\APIBundle\Model\Forum', $boardChild);
        }
    }

    public function testParseSubBoard()
    {
        $boardContent = file_get_contents(__DIR__.'/../InputSamples/forum-sub-2.html');

        $boardIndex = ForumParser::parseSubBoards($boardContent);

        //Some sanity checking on the pages and main content
        $this->assertInternalType('array', $boardIndex);
        $this->assertGreaterThan(0, $boardIndex['pages']);
        $this->assertInternalType('array', $boardIndex['list']);

        $topic = $boardIndex['list'][0];

        //Some sanity checking on the topic details
        $this->assertInstanceOf('Atarashii\APIBundle\Model\Forum', $topic);
        $this->assertInternalType('int', $topic->getId());
        $this->assertInternalType('string', $topic->getName());
        $this->assertInternalType('string', $topic->getUsername());
        $this->assertInternalType('int', $topic->getReplies());
        $this->assertInstanceOf('\DateTime', new \DateTime($topic->getTime()));

        //Some sanity checking on the reply class in the topic details
        $topicReply = $topic->getReply();
        $this->assertInternalType('array', $topicReply);
        $this->assertInternalType('string', $topicReply['username']);
        $this->assertInstanceOf('\DateTime', new \DateTime($topicReply['time']));
    }

    public function testParseTopics()
    {
        $boardContent = file_get_contents(__DIR__.'/../InputSamples/forum-board-14.html');

        $board = ForumParser::parseTopics($boardContent);

        //Some sanity checking on the pages and main content
        $this->assertInternalType('array', $board);
        $this->assertGreaterThan(0, $board['pages']);
        $this->assertInternalType('array', $board['list']);

        $topic = $board['list'][0];

        //Some sanity checking on the topic
        $this->assertInstanceOf('Atarashii\APIBundle\Model\Forum', $topic);
        $this->assertInternalType('int', $topic->getId());
        $this->assertInternalType('string', $topic->getUsername());
        $this->assertInternalType('string', $topic->getName());
        $this->assertInternalType('string', $topic->getUsername());
        $this->assertInternalType('int', $topic->getReplies());
        $this->assertInstanceOf('\DateTime', new \DateTime($topic->getTime()));

        //Some sanity checking on the reply class in the topic details
        $topicReply = $topic->getReply();
        $this->assertInternalType('array', $topicReply);
        $this->assertInternalType('string', $topicReply['username']);
        $this->assertInstanceOf('\DateTime', new \DateTime($topicReply['time']));
    }

    public function testParseTopic()
    {
        $topicContent = file_get_contents(__DIR__.'/../InputSamples/forum-topic-516059.html');

        $topic = ForumParser::parseTopic($topicContent);

        //Some sanity checking on the pages and main content
        $this->assertInternalType('array', $topic);
        $this->assertGreaterThan(0, $topic['pages']);
        $this->assertInternalType('array', $topic['list']);

        $comment = $topic['list'][0];

        //Some sanity checking on the topic details
        $this->assertInstanceOf('Atarashii\APIBundle\Model\Forum', $comment);
        $this->assertInternalType('int', $comment->getId());
        $this->assertInternalType('string', $comment->getUsername());
        $this->assertInternalType('string', $comment->getComment());
        $this->assertInstanceOf('\DateTime', new \DateTime($comment->getTime()));

        //Some sanity checking on profile class in the topic details
        $this->assertInstanceOf('Atarashii\APIBundle\Model\Profile', $comment->profile);
        $this->assertInternalType('string', $comment->profile->getAvatarUrl());
        $this->assertInternalType('string', $comment->profile->details->getStatus());
        $this->assertInstanceOf('\DateTime', new \DateTime($comment->profile->details->getJoinDate()));
        $this->assertInternalType('string', $comment->profile->details->getAccessRank());
        $this->assertInternalType('string', $comment->profile->details->getForumPosts());
    }
}
