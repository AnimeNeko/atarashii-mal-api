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
use Atarashii\APIBundle\Model\Forum;
use Atarashii\APIBundle\Model\Profile;
use Atarashii\APIBundle\Helper\Date;

class ForumParser
{
    public static function parseBoard($contents)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $category = $crawler->filter('div[class="forum-board-list pb16"]');
        foreach ($category as $item) {
            $crawler = new Crawler($item);
            $categoryHeader = $crawler->filter('div[class="forum-header"]')->text();
            $boarditems = $crawler->filter('div[class="forum-board"]');
            foreach ($boarditems as $boardParseItem) {
                $result[$categoryHeader][] = self::parseBoards($boardParseItem);
            }
        }

        return $result;
    }

    private static function parseBoards($item)
    {
        $crawler = new Crawler($item);
        $board = new Forum();

        // contains no childeren
        if ($crawler->filter('span[class="forum-subboards"]')->count() < 1) {
            # name.
            $board->setName($crawler->filter('a')->text());

            # id.
            $board->setId(str_replace('https://myanimelist.net/forum/?board=', '', $crawler->filter('a')->attr('href')));

            # description.
            $board->setDescription($crawler->filter('span')->text());
        } else {
            # name.
            $board->setName($crawler->filter('span')->text());

            # description.
            $board->setDescription($crawler->filter('span[class="forum-board-description"]')->text());

            $childerenitems = $crawler->filter('span[class="forum-subboards"] a');
            foreach ($childerenitems as $children) {
                $crawler = new Crawler($children);
                $child = new Forum();

                # name.
                $child->setName($crawler->filter('a')->text());

                # id.
                $child->setId(str_replace('https://myanimelist.net/forum/?subboard=', '', $crawler->attr('href')));

                $board->setChildren($child);
            }
        }

        return $board;
    }

    public static function parseSubBoards($contents)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $topicsitems = $crawler->filter('tr');
        foreach ($topicsitems as $item) {
            //Trick to force json array and not json objects.
            $set = self::parseTopicsDetails($item);
            if ($set !== null) {
                $resultset[] = $set;
            }
        }

        $result['pages'] = 1;
        $result['list'] = $resultset;

        return $result;
    }

    public static function parseTopics($contents)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $topicsitems = $crawler->filter('tr');
        foreach ($topicsitems as $item) {
            //Trick to force json array and not json objects.
            $set = self::parseTopicsDetails($item);
            if ($set !== null) {
                $resultset[] = $set;
            }
        }

        try {
            $pages = $crawler->filter('span[class="di-ib"]')->text();
            if (strlen($pages) > 0) {
                preg_match('/\((\d*?)\)/', $pages, $pageNumber);
                $result['pages'] = (int) $pageNumber[1];
            } else {
                $result['pages'] = 1;
            }
        } catch (\InvalidArgumentException $e) {
            //do nothing
        }
        $result['list'] = $resultset;

        return $result;
    }

    private static function parseTopicsDetails($item)
    {
        $crawler = new Crawler($item);
        if ($crawler->filter('td')->count() >= 4) {
            $topics = new Forum();

            # id.
            # Example:
            # <span id="wt439011">...</span>
            $topics->setId(str_replace('/forum/?topicid=', '', $crawler->filter('td[class="forum_boardrow1"] a')->attr('href')));//->filter('span')->attr('id')));

            # name.
            # Example:
            # <a href="/forum/?topicid=439011">BBCode Fixes</a>
            $topics->setName($crawler->filter('td[class="forum_boardrow1"] a')->text());

            # username.
            # Example:
            # <a href="/profile/ratan12">ratan12</a>
            $topics->setUsername(str_replace('?board=', '', $crawler->filter('span[class="forum_postusername"] a')->text()));

            # replies.
            # Example:
            # <td align="center" width="75" class="forum_boardrow2" style="border-width: 0px 1px 1px 0px;">159</td>
            $topics->setReplies(str_replace('?board=', '', $crawler->filter('td[class="forum_boardrow2"]')->eq(1)->text()));

            # creation time.
            # Example:
            # <span class="lightLink">Jun 25, 2008</span>
            $topics->setTime($crawler->filter('span[class="lightLink"]')->text());

            //note: eq(1) is the second node and !first.
            $username = $crawler->filter('td[class="forum_boardrow1"]')->eq(1)->filter('a')->text();
            $time = explode("»»", $crawler->filter('td[class="forum_boardrow1"]')->eq(1)->text());

            $topics->setReply(array('username' => $username, 'time' => Date::formatTime($time[1])));

            return $topics;
        } else {
            return;
        }
    }

    public static function parseTopic($contents)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $topicitems = $crawler->filter('div[class="forum_border_around "]');
        foreach ($topicitems as $item) {
            $set[] = self::parseTopicDetails($item);
        }

        $pages = $crawler->filter('div[class="fl-r pb4"]')->text();
        if ($pages != '') {
            $result['pages'] = ((int) substr($pages, strpos($pages, ' (') + 2, strpos($pages, ')')));
        } else {
            $result['pages'] = 1;
        }
        $result['list'] = $set;

        return $result;
    }

    private static function parseTopicDetails($item)
    {
        $crawler = new Crawler($item);
        $topic = new Forum();
        $topic->profile = new Profile();

        $topic->setTime($crawler->filter('div[style="padding-left: 3px;"]')->text());

        # message id.
        # Example:
        # <div class="forum_border_around" id="forumMsg30902219">...</div>
        $topic->setid(str_replace('forumMsg', '', $crawler->attr('id')));

        # image url.
        # Example:
        # <img src="http://cdn.myanimelist.net/images/useravatars/1901304.jpg" vspace="2" border="0">
        //Note: Some MAL users do not have any avatars in the forum!
        try {
            $topic->profile->setAvatarUrl($crawler->filter('img')->attr('src'));
        } catch (\InvalidArgumentException $e) {
            //do nothing
        }

        $details = explode("\n\t\t  ", $crawler->filter('td[class="forum_boardrow2"]')->text());
        $topic->setUsername($details[0]);
        $topic->profile->details->setForumPosts(str_replace('Posts: ', '', $details[6]));
        if ($details[1] == '') {
            $topic->profile->details->setAccessRank('Member');
        } else {
            $topic->profile->details->setAccessRank($details[1]);
        }

        if ($topic->profile->details->getForumPosts() == '') {
            $topic->profile->details->setStatus($details[3]);
            $topic->profile->details->setJoinDate(str_replace('Joined: ', '', $details[4]));
            $topic->profile->details->setForumPosts(str_replace('Posts: ', '', $details[5]));
        } else {
            $topic->profile->details->setStatus($details[4]);
            $topic->profile->details->setJoinDate(str_replace('Joined: ', '', $details[5]));
        }

        //to force json array and !objects.
        $topic->profile->manga_stats = null;
        $topic->profile->anime_stats = null;

        # comment.
        # Example:
        # <div id="message25496275">...</div>
        $topic->setComment($crawler->filter('div[id="message'.$topic->getId().'"]')->html());

        return $topic;
    }
}
