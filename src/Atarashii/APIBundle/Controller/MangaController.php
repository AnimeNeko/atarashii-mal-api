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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Http\Exception;
use Atarashii\APIBundle\Parser\MangaParser;
use Atarashii\APIBundle\Parser\ReviewParser;
use JMS\Serializer\SerializationContext;

class MangaController extends FOSRestController
{
    /**
     * Get the details of a manga
     *
     * @param int     $id The ID of the manga as assigned by MyAnimeList
     * @param string  $apiVersion The API version of the request
     * @param Request $request HTTP Request object
     *
     * @return View
     */
    public function getAction($id, $apiVersion, Request $request)
    {
        // http://myanimelist.net/manga/#{id}

        $usepersonal = (int) $request->query->get('mine');

        $downloader = $this->get('atarashii_api.communicator');

        if ($usepersonal) {
            //get the credentials we received
            $username = $this->getRequest()->server->get('PHP_AUTH_USER');
            $password = $this->getRequest()->server->get('PHP_AUTH_PW');

            //Don't bother making a request if the user didn't send any authentication
            if ($username == null) {
                $view = $this->view(Array('error' => 'unauthorized'), 401);
                $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

                return $view;
            }

            try {
                $downloader->cookieLogin($username, $password);
            } catch (Exception\CurlException $e) {
                return $this->view(Array('error' => 'network-error'), 500);
            }
        }

        try {
            $mangadetails = $downloader->fetch('/manga/' . $id);
        } catch (Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
        }

        if (strpos($mangadetails, 'No manga found') !== false) {
            return $this->view(Array('error' => 'No manga found, check the manga id and try again.'), 404);
        } else {
            $manga = MangaParser::parse($mangadetails);

            $response = new Response();
            $serializationContext = SerializationContext::create();
            $serializationContext->setVersion($apiVersion);

            //For compatibility, API 1.0 explicitly passes null parameters.
            if ($apiVersion == "1.0") {
                $serializationContext->setSerializeNull(true);
            }

            //Only include cache info if it doesn't include personal data.
            if (!$usepersonal) {
                $response->setPublic();
                $response->setMaxAge(3600); //One hour
                $response->headers->addCacheControlDirective('must-revalidate', true);
                $response->setEtag('manga/' . $id);

                //Also, set "expires" header for caches that don't understand Cache-Control
                $date = new \DateTime();
                $date->modify('+3600 seconds'); //One hour
                $response->setExpires($date);
            }

            $view = $this->view($manga);

            $view->setSerializationContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Get the reviews of an manga
     *
     * @param int     $id The ID of the anime as assigned by MyAnimeList
     * @param Request $request HTTP Request object
     *
     * @return View
     */
    public function getReviewsAction($id, Request $request)
    {
        // http://myanimelist.net/manga/#{id}/ /reviews&p=#{page}

        $downloader = $this->get('atarashii_api.communicator');

        $page = ((int) $request->query->get('page')) - 1;
        if ($page < 0) {
            $page = 0;
        }

        try {
            $details = $downloader->fetch('/manga/' . $id . '/ /reviews&p=' . $page);
        } catch (Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
        }

        if (strpos($details, 'no reviews submitted') !== false) {
            return $this->view(Array('error' => 'There have been no reviews submitted for this manga yet.'), 200);
        } else {
            $reviews = ReviewParser::parse($details, 'M'); //M = Manga

            $response = new Response();
            $serializationContext = SerializationContext::create();
            $serializationContext->setSerializeNull(true);

            $response->setPublic();
            $response->setMaxAge(10800); //One hour
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->setEtag('manga/reviews/' . $id);

            //Also, set "expires" header for caches that don't understand Cache-Control
            $date = new \DateTime();
            $date->modify('+10800 seconds'); //One hour
            $response->setExpires($date);

            $view = $this->view($reviews);

            $view->setSerializationContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }
}
