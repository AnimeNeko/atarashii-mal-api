<?php
/**
* Atarashii MAL API
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/

namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Atarashii\APIBundle\Parser\ForumParser;

class ForumController extends FOSRestController
{

    /**
    * Get the forum board of MAL
    *
    * @return View
    */
    public function getForumBoardAction()
    {
        // http://myanimelist.net/forum/
        $downloader = $this->get('atarashii_api.communicator');

        try {
            $forumcontent = $downloader->fetch('/forum/index.php');
        } catch (\Guzzle\Http\Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
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
    * Get the forum topics of MAL
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

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $forumcontent = $downloader->fetch('/forum/index.php?board='.$id.'&show='.(($page*20)-20));
        } catch (\Guzzle\Http\Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
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
    * Get the topic of MAL
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

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $forumcontent = $downloader->fetch('/forum/?topicid='.$id.'&show='.(($page*20)-20));
        } catch (\Guzzle\Http\Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
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

}
