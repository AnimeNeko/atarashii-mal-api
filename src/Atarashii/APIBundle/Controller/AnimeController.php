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

use Atarashii\APIBundle\Parser\ReviewParser;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Http\Exception;
use Atarashii\APIBundle\Parser\AnimeParser;
use JMS\Serializer\SerializationContext;

class AnimeController extends FOSRestController
{
    /**
     * Get the details of an anime
     *
     * @param int     $id The ID of the anime as assigned by MyAnimeList
     * @param string  $apiVersion The API version of the request
     * @param Request $request HTTP Request object
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
            $animedetails = $downloader->fetch('/anime/' . $id);
        } catch (Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
        }

        if (strpos($animedetails, 'No series found') !== false) {
            return $this->view(Array('error' => 'No series found, check the series id and try again.'), 404);
        } else {
            $anime = AnimeParser::parse($animedetails);

            //Parse extended personal details if API 2.0 or better and personal details are requested
            if ($apiVersion >= "2.0" && $usepersonal) {

                try {
                    $animedetails = $downloader->fetch('/editlist.php?type=anime&id=' . $id . '&hideLayout=true');
                } catch (Exception\CurlException $e) {
                    return $this->view(Array('error' => 'network-error'), 500);
                }

                if (strpos($animedetails, 'This is not your entry') === false) {
                    $anime = AnimeParser::parseExtendedPersonal($animedetails, $anime);
                }
            }

            $response = new Response();
            $serializationContext = SerializationContext::create();
            $serializationContext->setVersion($apiVersion);

            //For compatibility, API 1.0 explicitly passes null parameters.
            if ($apiVersion == "1.0") {
                $serializationContext->setSerializeNull(true);
            }

            //After API 1.0, we don't show the "listed anime id" parameter
            //Always set it to null to hide it from the output.
            if ($apiVersion > 1.0) {
                $anime->setListedAnimeId(null);
            }

            //Only include cache info if it doesn't include personal data.
            if (!$usepersonal) {
                $response->setPublic();
                $response->setMaxAge(3600); //One hour
                $response->headers->addCacheControlDirective('must-revalidate', true);
                $response->setEtag('anime/' . $id);

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
     * Get the reviews of an anime
     *
     * @param int     $id The ID of the anime as assigned by MyAnimeList
     * @param Request $request HTTP Request object
     *
     * @return View
     */
    public function getReviewsAction($id, Request $request)
    {
        // http://myanimelist.net/anime/#{id}/ /reviews&p=#{page}
        $downloader = $this->get('atarashii_api.communicator');

        $page = ((int) $request->query->get('page')) - 1;
        if ($page < 0) {
            $page = 0;
        }

        try {
            $details = $downloader->fetch('/anime/' . $id . '/ /reviews&p=' . $page);
        } catch (Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
        }

        if (strpos($details, 'no reviews submitted') !== false) {
            return $this->view(Array('error' => 'There have been no reviews submitted for this anime yet.'), 200);
        } else {
            $reviews = ReviewParser::parse($details, 'A'); //A = Anime

            $response = new Response();
            $response->setPublic();
            $response->setMaxAge(10800); //One hour
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->setEtag('anime/reviews/' . $id . '?page=' . $page);

            //Also, set "expires" header for caches that don't understand Cache-Control
            $date = new \DateTime();
            $date->modify('+10800 seconds'); //One hour
            $response->setExpires($date);

            $view = $this->view($reviews);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }
}
