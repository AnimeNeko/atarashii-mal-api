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

use Atarashii\APIBundle\Parser\ReviewParser;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Http\Exception;
use Atarashii\APIBundle\Parser\AnimeParser;
use Atarashii\APIBundle\Parser\MangaParser;
use Atarashii\APIBundle\Parser\CastParser;
use Atarashii\APIBundle\Parser\HistoryParser;
use Atarashii\APIBundle\Parser\RecsParser;
use Atarashii\APIBundle\Parser\EpsParser;
use Atarashii\APIBundle\Parser\ScheduleParser;
use Atarashii\APIBundle\Helper\Date;
use JMS\Serializer\SerializationContext;

class RecordController extends FOSRestController
{
    /**
     * Get the details of an anime or manga.
     *
     * @param int     $id          The ID of the anime or manga as assigned by MyAnimeList
     * @param string  $apiVersion  The API version of the request
     * @param Request $request     HTTP Request object
     * @param string  $requestType The anime or manga request string
     *
     * @return View
     */
    public function getAction($id, $apiVersion, $requestType, Request $request)
    {
        //General information (and basic personal information) at:
        // http://myanimelist.net/anime/#{id}
        // http://myanimelist.net/manga/#{id}
        //Detailed personal information at:
        // http://myanimelist.net/ownlist/anime/{id}/edit?hideLayout
        // http://myanimelist.net/ownlist/manga/{id}/edit?hideLayout

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
            $recordDetails = $downloader->fetch('/'.$requestType.'/'.$id);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        } catch (Exception\ClientErrorResponseException $e) {
            $recordDetails = $e->getResponse();
        }

        if ((strpos($recordDetails, 'No series found') !== false) || (strpos($recordDetails, 'This page doesn\'t exist') !== false)) {
            return $this->view(array('error' => 'not-found'), 404);
        } else {
            if ($requestType === 'anime') {
                $record = AnimeParser::parse($recordDetails, $apiVersion);
            } else {
                $record = MangaParser::parse($recordDetails, $apiVersion);
            }

            //Parse extended personal details if API 2.0 or better and personal details are requested
            if ($apiVersion >= '2.0' && $usepersonal) {
                try {
                    $recordDetails = $downloader->fetch('/ownlist/'.$requestType.'/'.$id.'/edit?hideLayout');
                } catch (Exception\CurlException $e) {
                    return $this->view(array('error' => 'network-error'), 500);
                }

                if (strpos($recordDetails, 'delete-form') !== false) {
                    if ($requestType === 'anime') {
                        $record = AnimeParser::parseExtendedPersonal($recordDetails, $record);
                    } else {
                        $record = MangaParser::parseExtendedPersonal($recordDetails, $record);
                    }
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
                $response->setEtag($requestType.'/'.$id);

                //Also, set "expires" header for caches that don't understand Cache-Control
                $date = new \DateTime();
                $date->modify('+3600 seconds'); //One hour
                $response->setExpires($date);
            }

            $view = $this->view($record);

            $view->setSerializationContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Get the reviews of an anime or manga.
     *
     * If there isn't any page passed it will use the most helpfull voted reviews.
     * These are determined by the ratio (helpfull:all).
     *
     * @param int     $id          The ID of the anime or manga as assigned by MyAnimeList
     * @param Request $request     HTTP Request object
     * @param string  $requestType The anime or manga request string
     *
     * @return View
     */
    public function getReviewsAction($id, $requestType, Request $request)
    {
        // http://myanimelist.net/anime/#{id}/ /reviews&p=#{page}
        // http://myanimelist.net/manga/#{id}/ /reviews&p=#{page}
        $downloader = $this->get('atarashii_api.communicator');

        $page = ((int) $request->query->get('page'));
        if ($page < 0) {
            $page = 0;
        }

        try {
            $details = $downloader->fetch('/'.$requestType.'/'.$id.'/_/reviews&p='.$page);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(10800); //Three hour
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag($requestType.'/reviews/'.$id.'?page='.$page);

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+10800 seconds'); //Three hour
        $response->setExpires($date);

        if (strpos($details, 'No series found, check the series id and try again.') !== false) {
            $view = $this->view(array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {
            $reviews = ReviewParser::parse($details, $requestType);

            $view = $this->view($reviews);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Get the cast of an anime or manga.
     *
     * @param int    $id          The ID of the anime or manga as assigned by MyAnimeList
     * @param string $requestType The anime or manga request string
     *
     * @return View
     */
    public function getCastAction($id, $requestType)
    {
        // http://myanimelist.net/anime/#{id}/ /characters
        // http://myanimelist.net/manga/#{id}/ /characters
        $downloader = $this->get('atarashii_api.communicator');

        try {
            $details = $downloader->fetch('/'.$requestType.'/'.$id.'/_/characters');
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        if (strpos($details, 'No characters') !== false) {
            return $this->view(array('error' => 'not-found'), 200);
        } else {
            $cast = CastParser::parse($details);

            $response = new Response();
            $response->setPublic();
            $response->setMaxAge(86400); //One day
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->setEtag($requestType.'/cast/'.$id);

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

    /**
     * Get the watching history of an anime or manga.
     *
     * @param int    $id          The ID of the anime or manga as assigned by MyAnimeList
     * @param string $requestType The anime or manga request string
     *
     * @return View
     */
    public function getHistoryAction($id, $requestType)
    {
        // http://myanimelist.net/ajaxtb.php?detailedaid=#{id}
        // http://myanimelist.net/ajaxtb.php?detailedmid=#{id}

        $downloader = $this->get('atarashii_api.communicator');

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        //Don't bother making a request if the user didn't send any authentication
        if ($username === null || $password === null || $username === '' || $password === '') {
            $view = $this->view(array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        }

        try {
            if (!$downloader->cookieLogin($username, $password)) {
                $view = $this->view(array('error' => 'unauthorized'), 401);
                $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

                return $view;
            }
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        try {
            if ($requestType === 'anime') {
                $content = $downloader->fetch('/ajaxtb.php?detailedaid='.$id);
            } else {
                $content = $downloader->fetch('/ajaxtb.php?detailedmid='.$id);
            }
            Date::setTimeZone($downloader->fetch('/editprofile.php'));
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        } catch (Exception\ClientErrorResponseException $e) {
            $content = $e->getResponse();
        }

        if (strpos($content, 'Not logged in') !== false) {
            $view = $this->view(array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        } else {
            $result = HistoryParser::parse($content, $id, $requestType);

            $view = $this->view($result);
            $view->setResponse(new Response());
            $view->setStatusCode(200);
        }

        return $view;
    }

    /**
     * Get the recommendations of an anime or manga.
     *
     * @param int    $id          The ID of the anime or manga as assigned by MyAnimeList
     * @param string $requestType The anime or manga request string
     *
     * @return View
     */
    public function getRecsAction($id, $requestType)
    {
        // http://myanimelist.net/anime/#{id}/_/userrecs
        // http://myanimelist.net/manga/#{id}/_/userrecs
        $downloader = $this->get('atarashii_api.communicator');

        try {
            $details = $downloader->fetch('/'.$requestType.'/'.$id.'/_/userrecs');
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        if (strpos($details, 'No recommendations have been made for this title.') !== false) {
            return $this->view(array('error' => 'not-found'), 200);
        } else {
            $result = RecsParser::parse($details);

            $response = new Response();
            $response->setPublic();
            $response->setMaxAge(86400); //Two day
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->setEtag($requestType.'/recs/'.$id);

            //Also, set "expires" header for caches that don't understand Cache-Control
            $date = new \DateTime();
            $date->modify('+86400 seconds'); //Two days
            $response->setExpires($date);

            $view = $this->view($result);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Get the episodes of an anime.
     *
     * @param int     $id      The ID of the anime as assigned by MyAnimeList
     * @param Request $request HTTP Request object
     *
     * @return View
     */
    public function getEpsAction($id, Request $request)
    {
        // http://myanimelist.net/anime/#{id}/_/episode
        $downloader = $this->get('atarashii_api.communicator');

        $page = ((int) $request->query->get('page'));
        if ($page < 0) {
            $page = 1;
        }

        try {
            $details = $downloader->fetch('/anime/'.$id.'/_/episode?offset='.(($page * 100) - 100));
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        if (strpos($details, 'No episode information has been added to this title.') !== false) {
            return $this->view(array('error' => 'not-found'), 200);
        } else {
            $result = EpsParser::parse($details);

            $response = new Response();
            $response->setPublic();
            $response->setMaxAge(86400); //Two day
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->setEtag('anime/episodes/'.$id);

            //Also, set "expires" header for caches that don't understand Cache-Control
            $date = new \DateTime();
            $date->modify('+86400 seconds'); //Two days
            $response->setExpires($date);

            $view = $this->view($result);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Get the airing schedule.
     *
     * @param Request $request HTTP Request object
     *
     * @return View
     */
    public function getScheduleAction(Request $request)
    {
        // http://myanimelist.net/anime/#{id}/_/episode
        $downloader = $this->get('atarashii_api.communicator');

        $timeZone = $request->query->get('timezone');

        try {
            $details = $downloader->fetch('/anime/season/schedule');
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }
        
        $result = ScheduleParser::parse($details, $timeZone);

        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(43200); //one day
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag('anime/season/schedule'.$timeZone);

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+43200 seconds'); //one day
        $response->setExpires($date);

        $view = $this->view($result);
        $view->setResponse($response);
        $view->setStatusCode(200);

        return $view;
    }
}
