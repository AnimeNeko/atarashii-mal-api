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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Http\Exception;
use Atarashii\APIBundle\Parser\MangaParser;
use Atarashii\APIBundle\Parser\ReviewParser;
use Atarashii\APIBundle\Parser\CastParser;
use JMS\Serializer\SerializationContext;

class MangaController extends FOSRestController
{
    /**
     * Get the details of a manga.
     *
     * @param int     $id         The ID of the manga as assigned by MyAnimeList
     * @param string  $apiVersion The API version of the request
     * @param Request $request    HTTP Request object
     *
     * @return View
     */
    public function getAction($id, $apiVersion, Request $request)
    {
        //General information (and basic personal information) at:
        // http://myanimelist.net/manga/#{id}
        //Detailed personal information at:
        // http://myanimelist.net/panel.php?go=editmanga&id={listed id}&hidenav=true

        $usepersonal = (int) $request->query->get('mine');

        $downloader = $this->get('atarashii_api.communicator');

        if ($usepersonal) {
            //get the credentials we received
            $username = $this->getRequest()->server->get('PHP_AUTH_USER');
            $password = $this->getRequest()->server->get('PHP_AUTH_PW');

            try {
                if (!$downloader->cookieLogin($username, $password)) {
                    $view = $this->view(array('error' => 'unauthorized'), 401);
                    $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

                    return $view;
                }
            } catch (Exception\CurlException $e) {
                return $this->view(array('error' => 'network-error'), 500);
            }
        }

        try {
            $mangadetails = $downloader->fetch('/manga/'.$id);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        } catch (Exception\ClientErrorResponseException $e) {
            $mangadetails = $e->getResponse();
        }

        if ((strpos($mangadetails, 'No manga found') !== false) || (strpos($mangadetails, 'This page doesn\'t exist') !== false)) {
            return $this->view(array('error' => 'No manga found, check the manga id and try again.'), 404);
        } else {
            $manga = MangaParser::parse($mangadetails);

            //Parse extended personal details if API 2.0 or better and personal details are requested
            if ($apiVersion >= '2.0' && $usepersonal) {
                if ($manga->getListedMangaId() !== null) {
                    try {
                        $mangaDetails = $downloader->fetch('http://myanimelist.net/panel.php?go=editmanga&id='.$manga->getListedMangaId().'&hidenav=true');
                    } catch (Exception\CurlException $e) {
                        return $this->view(array('error' => 'network-error'), 500);
                    }

                    $manga = MangaParser::parseExtendedPersonal($mangaDetails, $manga);
                }
            }

            $response = new Response();
            $serializationContext = SerializationContext::create();
            $serializationContext->setVersion($apiVersion);

            //For compatibility, API 1.0 explicitly passes null parameters.
            if ($apiVersion == '1.0') {
                $serializationContext->setSerializeNull(true);
            }

            //Only include cache info if it doesn't include personal data.
            if (!$usepersonal) {
                $response->setPublic();
                $response->setMaxAge(3600); //One hour
                $response->headers->addCacheControlDirective('must-revalidate', true);
                $response->setEtag('manga/'.$id);

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
     * Get the reviews of a manga.
     *
     * @param int     $id      The ID of the manga as assigned by MyAnimeList
     * @param Request $request HTTP Request object
     *
     * @return View
     */
    public function getReviewsAction($id, $apiVersion, Request $request)
    {
        // http://myanimelist.net/manga/#{id}/ /reviews&p=#{page}

        $downloader = $this->get('atarashii_api.communicator');

        $page = ((int) $request->query->get('page')) - 1;
        if ($page < 0) {
            $page = 0;
        }

        try {
            $details = $downloader->fetch('/manga/'.$id.'/_/reviews&p='.$page);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        $response = new Response();
        $serializationContext = SerializationContext::create();
        $serializationContext->setVersion($apiVersion);

        $response->setPublic();
        $response->setMaxAge(10800); //Three hour
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag('manga/reviews/'.$id.'?page='.$page);

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+10800 seconds'); //Three hour
        $response->setExpires($date);

        if (strpos($details, 'No manga found, check the manga id and try again.') !== false) {
            $view = $this->view(array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } elseif (strpos($details, 'no reviews submitted') !== false) {
            $view = $this->view(array());
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        } else {
            $reviews = ReviewParser::parse($details, 'M'); //M = Manga

            $view = $this->view($reviews);
            $view->setSerializationContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Get the cast of a manga.
     *
     * @param int $id The ID of the manga as assigned by MyAnimeList
     *
     * @return View
     */
    public function getCastAction($id)
    {
        // http://myanimelist.net/anime/#{id}/ /characters
        $downloader = $this->get('atarashii_api.communicator');

        try {
            $details = $downloader->fetch('/manga/'.$id.'/_/characters');
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        if (strpos($details, 'No characters') !== false) {
            return $this->view(array('error' => 'No characters were found. '), 200);
        } else {
            $cast = CastParser::parse($details);

            $response = new Response();
            $response->setPublic();
            $response->setMaxAge(86400); //One day
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->setEtag('manga/cast/'.$id);

            //Also, set "expires" header for caches that don't understand Cache-Control
            $date = new \DateTime();
            $date->modify('+86400 seconds'); //One day
            $response->setExpires($date);

            $view = $this->view($cast);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }
}
