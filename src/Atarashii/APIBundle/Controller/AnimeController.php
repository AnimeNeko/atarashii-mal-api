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
use Atarashii\APIBundle\Parser\CastParser;
use Atarashii\APIBundle\Parser\HistoryParser;
use Atarashii\APIBundle\Parser\RecsParser;
use Atarashii\APIBundle\Helper\Date;
use JMS\Serializer\SerializationContext;

class AnimeController extends FOSRestController
{
    /**
     * Get the details of an anime.
     *
     * @param int     $id         The ID of the anime as assigned by MyAnimeList
     * @param string  $apiVersion The API version of the request
     * @param Request $request    HTTP Request object
     *
     * @return View
     */
    public function getAction($id, $apiVersion, Request $request)
    {
        //General information (and basic personal information) at:
        // http://myanimelist.net/anime/#{id}
        //Detailed personal information at:
        // http://myanimelist.net/editlist.php?type=anime&id={id}&hideLayout=true

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
            $animedetails = $downloader->fetch('/anime/'.$id);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        } catch (Exception\ClientErrorResponseException $e) {
            $animedetails = $e->getResponse();
        }

        if ((strpos($animedetails, 'No series found') !== false) || (strpos($animedetails, 'This page doesn\'t exist') !== false)) {
            return $this->view(array('error' => 'No series found, check the series id and try again.'), 404);
        } else {
            $anime = AnimeParser::parse($animedetails, $apiVersion);

            //Parse extended personal details if API 2.0 or better and personal details are requested
            if ($apiVersion >= '2.0' && $usepersonal) {
                try {
                    $animedetails = $downloader->fetch('/editlist.php?type=anime&id='.$id.'&hideLayout=true');
                } catch (Exception\CurlException $e) {
                    return $this->view(array('error' => 'network-error'), 500);
                }

                if (strpos($animedetails, 'delete-form') !== false) {
                    $anime = AnimeParser::parseExtendedPersonal($animedetails, $anime);
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
                $response->setEtag('anime/'.$id);

                //Also, set "expires" header for caches that don't understand Cache-Control
                $date = new \DateTime();
                $date->modify('+3600 seconds'); //One hour
                $response->setExpires($date);
            }

            $view = $this->view($anime);

            $view->setSerializationContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Get the reviews of an anime.
     *
     * If there isn't any page passed it will use the most helpfull voted reviews.
     * These are determined by the ratio (helpfull:all).
     *
     * @param int     $id      The ID of the anime as assigned by MyAnimeList
     * @param Request $request HTTP Request object
     *
     * @return View
     */
    public function getReviewsAction($id, Request $request)
    {
        // http://myanimelist.net/anime/#{id}/ /reviews&p=#{page}
        $downloader = $this->get('atarashii_api.communicator');

        $page = ((int) $request->query->get('page'));
        if ($page < 0) {
            $page = 0;
        }

        try {
            $details = $downloader->fetch('/anime/'.$id.'/_/reviews&p='.$page);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(10800); //Three hour
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag('anime/reviews/'.$id.'?page='.$page);

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
            $reviews = ReviewParser::parse($details, 'A'); //A = Anime

            $view = $this->view($reviews);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Get the cast of an anime.
     *
     * @param int $id The ID of the anime as assigned by MyAnimeList
     *
     * @return View
     */
    public function getCastAction($id)
    {
        // http://myanimelist.net/anime/#{id}/ /characters
        $downloader = $this->get('atarashii_api.communicator');

        try {
            $details = $downloader->fetch('/anime/'.$id.'/_/characters');
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
            $response->setEtag('anime/cast/'.$id);

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
     * Get the watching history of an anime.
     *
     * @param int $id The ID of the anime as assigned by MyAnimeList
     *
     * @return View
     */
    public function getHistoryAction($id)
    {
        // http://myanimelist.net/ajaxtb.php?detailedaid=#{id}

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
            $content = $downloader->fetch('/ajaxtb.php?detailedaid='.$id);
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
            $result = HistoryParser::parse($content, $id, 'anime');

            $view = $this->view($result);
            $view->setResponse(new Response());
            $view->setStatusCode(200);
        }

        return $view;
    }

    /**
     * Get the recommendations of an anime.
     *
     * @param int $id The ID of the anime as assigned by MyAnimeList
     *
     * @return View
     */
    public function getRecsAction($id)
    {
        // http://myanimelist.net/anime/#{id}/_/userrecs
        $downloader = $this->get('atarashii_api.communicator');

        try {
            $details = $downloader->fetch('/anime/'.$id.'/_/userrecs');
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        if (strpos($details, 'No recommendations have been made for this title.') !== false) {
            return $this->view(array('error' => 'No recommendations were found. '), 200);
        } else {
            $result = RecsParser::parse($details);

            $response = new Response();
            $response->setPublic();
            $response->setMaxAge(86400); //Two day
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->setEtag('anime/recs/'.$id);

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
}
