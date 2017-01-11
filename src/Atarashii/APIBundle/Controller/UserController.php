<?php
/**
* Atarashii MAL API.
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014-2016 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Exception;
use Atarashii\APIBundle\Parser\User;
use FOS\RestBundle\Context\Context;

class UserController extends FOSRestController
{
    /**
     * Get the details for a username.
     *
     * @param string $apiVersion The API version of the request
     * @param string $username   The MyAnimeList username of the user.
     *
     * @return View
     */
    public function getProfileAction($apiVersion, $username)
    {
        // http://myanimelist.net/profile/#{username}

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $profilecontent = $downloader->fetch('/profile/'.$username);
        } catch (Exception\ServerException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        } catch (Exception\ClientException $e) {
            $profilecontent = $e->getResponse()->getBody();
        }

        $response = new Response();
        $serializationContext = new Context();
        $serializationContext->setVersion($apiVersion);

        //For compatibility, API 1.0 explicitly passes null parameters.
        if ($apiVersion == '1.0') {
            $serializationContext->setSerializeNull(true);
        }

        if (strpos($profilecontent, 'Failed to find') !== false || (strpos($profilecontent, 'This page doesn\'t exist') !== false)) {
            $view = $this->view(array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {
            $userprofile = User::parse($profilecontent, $apiVersion);

            $response->setPublic();
            $response->setMaxAge(900); //15 minutes
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->setEtag(md5(serialize($userprofile)));

            //Also, set "expires" header for caches that don't understand Cache-Control
            $date = new \DateTime();
            $date->modify('+900 seconds'); //15 minutes
            $response->setExpires($date);

            $view = $this->view($userprofile);

            $view->setContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Get a list of friends of the specified username.
     *
     * Returns a view of user objects constituting friends of the specified user. Sorting
     * is MyAnimeList default, in order of the most recently active user.
     *
     * @param string $apiVersion The API version of the request
     * @param string $username   The MyAnimeList username of the user.
     *
     * @return View
     */
    public function getFriendsAction($apiVersion, $username)
    {
        // http://myanimelist.net/profile/#{username}/friends

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $friendscontent = $downloader->fetch('/profile/'.$username.'/friends');
        } catch (Exception\ServerException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        } catch (Exception\ClientException $e) {
            $friendscontent = $e->getResponse()->getBody();
        }

        $response = new Response();
        $serializationContext = new Context();
        $serializationContext->setVersion($apiVersion);

        //For compatibility, API 1.0 explicitly passes null parameters.
        if ($apiVersion == '1.0') {
            $serializationContext->setSerializeNull(true);
        }

        if (strpos($friendscontent, 'Failed to find') !== false) {
            $view = $this->view(array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {
            if (strpos($friendscontent, 'No friends found') === false) {
                $friendlist = User::parseFriends($friendscontent);
            } else {
                $friendlist = array();
            }

            $response->setPublic();
            $response->setMaxAge(900); //15 minutes
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->setEtag(md5(serialize($friendlist)));

            //Also, set "expires" header for caches that don't understand Cache-Control
            $date = new \DateTime();
            $date->modify('+900 seconds'); //15 minutes
            $response->setExpires($date);

            $view = $this->view($friendlist);

            $view->setContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Get a list of anime/manga history of the specified username.
     *
     * Returns a view of history objects constituting history of the specified user. Sorting
     * is MyAnimeList default, in order of the leastest update.
     *
     * @param string $username The MyAnimeList username of the user.
     *
     * @return View
     */
    public function getHistoryAction($username)
    {
        // http://myanimelist.net/history/#{username}

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $historycontent = $downloader->fetch('/history/'.$username);
        } catch (Exception\ServerException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        if (strpos($historycontent, 'Invalid member username provided') !== false) {
            $view = $this->view(array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } elseif (strpos($historycontent, 'No history found') !== false) {
            $view = $this->view(array());
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        } else {
            $historylist = User::parseHistory($historycontent);

            $response = new Response();
            $response->setPublic();
            $response->setMaxAge(900); //15 minutes
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->setEtag(md5(serialize($historylist)));

            //Also, set "expires" header for caches that don't understand Cache-Control
            $date = new \DateTime();
            $date->modify('+900 seconds'); //15 minutes
            $response->setExpires($date);

            $view = $this->view($historylist);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }
}
