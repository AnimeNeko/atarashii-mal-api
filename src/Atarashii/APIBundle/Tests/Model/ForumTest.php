<?php

namespace Atarashii\APIBundle\Tests\Model;

use Atarashii\APIBundle\Model\Forum;

class ForumTest extends \PHPUnit_Framework_TestCase
{
    public function testId()
    {
        $forumId = rand();

        $forum = new Forum();
        $forum->setId($forumId);

        $this->assertEquals($forumId, $forum->getId());
    }

    public function testName()
    {
        $forumName = 'The Anime Bored';

        $forum = new Forum();
        $forum->setName($forumName);

        $this->assertEquals($forumName, $forum->getName());
    }

    public function testUsername()
    {
        $forumUname = 'Someone';

        $forum = new Forum();
        $forum->setUsername($forumUname);

        $this->assertEquals($forumUname, $forum->getUsername());
    }

    public function testReplies()
    {
        $forumReplies = 25;

        $forum = new Forum();
        $forum->setReplies($forumReplies);

        $this->assertEquals($forumReplies, $forum->getReplies());
    }

    public function testDescription()
    {
        $forumDesc = 'Description here';

        $forum = new Forum();
        $forum->setDescription($forumDesc);

        $this->assertEquals($forumDesc, $forum->getDescription());
    }

    public function testReply()
    {
        $now = new \DateTime('now');

        $reply = array();
        $reply['username'] = 'SomeUser';
        $reply['time'] = $now->format('Y-m-d\TH:iO');

        $forum = new Forum();

        $forum->setReply($reply);

        $this->assertEquals($reply, $forum->getReply());
    }

    public function testChildren()
    {
        $child = new Forum();
        $forum = new Forum();

        $child->setId(3);
        $child->setName('I am the child');

        $forum->setChildren($child);

        $children = $forum->getChildren();

        $this->assertEquals($child, $children[0]);
    }

    public function testComment()
    {
        $forumComment = 'This is a comment.';

        $forum = new Forum();
        $forum->setComment($forumComment);

        $this->assertEquals($forumComment, $forum->getComment());
    }

    public function testTime()
    {
        /* This tests multiple formats. Ideally, all the format checking
         * should be done testing the date helper, but we'll do this
         * here for now.
         */

        $forum = new Forum();

        // Test #1, relative time
        $forumTime = '7 minutes ago';

        $verifyTime = new \DateTime('NOW');
        $verifyTime->add(\DateInterval::createFromDateString($forumTime));

        $forum->setTime($forumTime);

        $this->assertEquals($verifyTime->format('Y-m-d\TH:iO'), $forum->getTime());

        // Test #2, partial date (same year)
        $forumTime = 'Jun 17, 1:01 PM';

        $currentTime = new \DateTime('NOW');
        $currentYear = $currentTime->format('Y');

        $verifyTime = new \DateTime($currentYear.'-06-17 13:01');

        $forum->setTime($forumTime);

        $this->assertEquals($verifyTime->format('Y-m-d\TH:iO'), $forum->getTime());

        // Test #3, full date
        $forumTime = ' May 1, 2014 1:19 PM';

        $verifyTime = new \DateTime('2014-05-01 13:19');

        $forum->setTime($forumTime);

        $this->assertEquals($verifyTime->format('Y-m-d\TH:iO'), $forum->getTime());
    }

    public static function setUpBeforeClass()
    {
        //Our date tests assume we're in the default timezone for MAL
        //Set the PHP timezone to America/Los_Angeles to be safe.
        date_default_timezone_set('America/Los_Angeles');
    }
}
