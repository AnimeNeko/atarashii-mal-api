<?php
/**
* Atarashii MAL API.
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014-2015 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Guzzle\Http\Exception;
use Atarashii\APIBundle\Parser\ForumParser;

class ForumController extends FOSRestController
{
    /**
     * Get the forum board of MAL.
     *
     * @return View
     */
    public function getForumBoardAction()
    {
        // http://myanimelist.net/forum/
        $downloader = $this->get('atarashii_api.communicator');

        try {
            $forumcontent = $downloader->fetch('/forum/index.php');
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        $forumboard = ForumParser::parseBoard($forumcontent);

        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(86400); //One day
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag('forum/board');

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+86400 seconds'); //One day
        $response->setExpires($date);

        $view = $this->view($forumboard);
        $view->setResponse($response);
        $view->setStatusCode(200);

        return $view;
    }

    /**
     * Get the forum sub board of MAL.
     *
     * @param Request $request HTTP Request object
     * @param int     $id      The ID of the forum sub board as assigned by MyAnimeList
     *
     * @return View
     */
    public function getForumSubBoardAction(Request $request, $id)
    {
        // http://myanimelist.net/forum/?subboard=#{id}
        $page = (int) $request->query->get('page');
        if ($page <= 0) {
            $page = 1;
        }
        if ((int) $id == '') {
            return $this->view(array('error' => 'Invalid board ID'), 200);
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $forumcontent = $downloader->fetch('/forum/?subboard='.$id.'&show='.(($page * 50) - 50));
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        if ($id == 1 || $id == 4) {
            $forumtopic = ForumParser::parseSubBoards($forumcontent);
        } else {
            $forumtopic = ForumParser::parseTopics($forumcontent);
        }

        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(300); //5 minutes
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag('forum/topic/'.$id);

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+300 seconds'); //5 minutes
        $response->setExpires($date);

        $view = $this->view($forumtopic);
        $view->setResponse($response);
        $view->setStatusCode(200);

        return $view;
    }

    /**
     * Get the forum topics of MAL.
     *
     * @param Request $request HTTP Request object
     * @param int     $id      The ID of the topic as assigned by MyAnimeList
     *
     * @return View
     */
    public function getForumTopicsAction(Request $request, $id)
    {
        // http://myanimelist.net/forum/index.php?board=#{id}

        $page = (int) $request->query->get('page');
        if ($page <= 0) {
            $page = 1;
        }

        if ((int) $id == '') {
            return $this->view(array('error' => 'Invalid board ID'), 200);
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $forumcontent = $downloader->fetch('/forum/index.php?board='.$id.'&show='.(($page * 50) - 50));
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        } catch (Exception\ClientErrorResponseException $e) {
            return $this->view(array('error' => 'Invalid board ID or page number'), 200);
        }

        $forumtopics = ForumParser::parseTopics($forumcontent);

        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(900); //15 minutes
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag('forum/'.$id);

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+900 seconds'); //15 minutes
        $response->setExpires($date);

        $view = $this->view($forumtopics);
        $view->setResponse($response);
        $view->setStatusCode(200);

        return $view;
    }

    /**
     * Get the Anime discussion forum topics of MAL.
     *
     * @param Request $request HTTP Request object
     * @param int     $id      The ID of the topic as assigned by MyAnimeList
     *
     * @return View
     */
    public function getForumAnimeAction(Request $request, $id)
    {
        // http://myanimelist.net/forum/?animeid=#{id}

        $page = (int) $request->query->get('page');
        if ($page <= 0) {
            $page = 1;
        }

        if ((int) $id == '') {
            return $this->view(array('error' => 'Invalid board ID'), 200);
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $forumcontent = $downloader->fetch('/forum/?animeid='.$id.'&show='.(($page * 50) - 50));
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        $forumtopics = ForumParser::parseTopics($forumcontent);

        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(900); //15 minutes
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag('forum/anime/'.$id);

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+900 seconds'); //15 minutes
        $response->setExpires($date);

        $view = $this->view($forumtopics);
        $view->setResponse($response);
        $view->setStatusCode(200);

        return $view;
    }

    /**
     * Get the manga discussion forum topics of MAL.
     *
     * @param Request $request HTTP Request object
     * @param int     $id      The ID of the topic as assigned by MyAnimeList
     *
     * @return View
     */
    public function getForumMangaAction(Request $request, $id)
    {
        // http://myanimelist.net/forum/?mangaid=#{id}

        $page = (int) $request->query->get('page');
        if ($page <= 0) {
            $page = 1;
        }

        if ((int) $id == '') {
            return $this->view(array('error' => 'Invalid board ID'), 200);
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $forumcontent = $downloader->fetch('/forum/?mangaid='.$id.'&show='.(($page * 50) - 50));
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        $forumtopics = ForumParser::parseTopics($forumcontent);

        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(900); //15 minutes
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag('forum/manga/'.$id);

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+900 seconds'); //15 minutes
        $response->setExpires($date);

        $view = $this->view($forumtopics);
        $view->setResponse($response);
        $view->setStatusCode(200);

        return $view;
    }

    /**
     * Get the topic of MAL.
     *
     * @param Request $request HTTP Request object
     * @param int     $id      The ID of the forum topic as assigned by MyAnimeList
     *
     * @return View
     */
    public function getForumTopicAction(Request $request, $id)
    {
        // http://myanimelist.net/forum/?topicid=#{id}

        $page = (int) $request->query->get('page');
        if ($page <= 0) {
            $page = 1;
        }
        if ((int) $id == '') {
            return $this->view(array('error' => 'Invalid topic ID'), 200);
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $forumcontent = $downloader->fetch('/forum/?topicid='.$id.'&show='.(($page * 50) - 50));
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        $forumtopic = ForumParser::parseTopic($forumcontent);

        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(300); //5 minutes
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag('forum/topic/'.$id);

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+300 seconds'); //5 minutes
        $response->setExpires($date);

        $view = $this->view($forumtopic);
        $view->setResponse($response);
        $view->setStatusCode(200);

        return $view;
    }

    /**
     * create a topic on MAL.
     *
     * @param Request $request HTTP Request object
     * @param int     $id      The ID of the forum board as assigned by MyAnimeList
     *
     * @return View
     */
    public function newTopicAction(Request $request, $id)
    {
        // http://myanimelist.net/forum/?action=post&boardid=#{id}

        $title = $request->request->get('title');
        $message = $request->request->get('message');
        if ($title == '' || $message == '') {
            return $this->view(array('error' => 'Invalid title or message'), 200);
        } elseif ((int) $id == '') {
            return $this->view(array('error' => 'Invalid board ID'), 200);
        }

        $downloader = $this->get('atarashii_api.communicator');

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        try {
            if (!$downloader->cookieLogin($username, $password)) {
                $view = $this->view(array('error' => 'unauthorized'), 401);
                $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

                return $view;
            }
            $topicdetails = $downloader->createTopic($id, $title, $message);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        if (strpos($topicdetails, 'successfully entered') !== false) {
            return $this->view(array('status' => 'OK'), 200);
        } else {
            return $this->view(array('error' => 'unknown'), 200);
        }
    }

    /**
     * create a comment on MAL topics.
     *
     * @param Request $request HTTP Request object
     * @param int     $id      The ID of the forum topic as assigned by MyAnimeList
     *
     * @return View
     */
    public function newCommentAction(Request $request, $id)
    {
        // http://myanimelist.net/forum/?action=message&topic_id=#{id}

        $message = $request->request->get('message');
        if ($message == '') {
            return $this->view(array('error' => 'Invalid message'), 200);
        } elseif ((int) $id == '') {
            return $this->view(array('error' => 'Invalid topic ID'), 200);
        }

        $downloader = $this->get('atarashii_api.communicator');

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        try {
            if (!$downloader->cookieLogin($username, $password)) {
                $view = $this->view(array('error' => 'unauthorized'), 401);
                $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

                return $view;
            }
            $topicdetails = $downloader->createComment($id, $message);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        if (strpos($topicdetails, 'Successfully posted') !== false) {
            return $this->view(array('status' => 'OK'), 200);
        } elseif (strpos($topicdetails, 'must contain 15 characters excluding') !== false) {
            return $this->view(array('error' => 'short message'), 200);
        } else {
            return $this->view(array('error' => 'unknown'), 200);
        }
    }

    /**
     * edith a comment on MAL topics.
     *
     * @param Request $request HTTP Request object
     * @param int     $id      The ID of the forum topic as assigned by MyAnimeList
     *
     * @return View
     */
    public function edithCommentAction(Request $request, $id)
    {
        // http://myanimelist.net/forum/?action=message&msgid=#{id}

        $message = $request->request->get('message');
        if ($message == '') {
            return $this->view(array('error' => 'Invalid message'), 200);
        } elseif ((int) $id == '') {
            return $this->view(array('error' => 'Invalid message ID'), 200);
        }

        $downloader = $this->get('atarashii_api.communicator');

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        try {
            if (!$downloader->cookieLogin($username, $password)) {
                $view = $this->view(array('error' => 'unauthorized'), 401);
                $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

                return $view;
            }
            $topicdetails = $downloader->edithComment($id, $message);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        if (strpos($topicdetails, 'Successfully edited') !== false) {
            return $this->view(array('status' => 'OK'), 200);
        } else {
            return $this->view(array('error' => 'unknown'), 200);
        }
    }
}
