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
use Atarashii\APIBundle\Parser\Top;
use JMS\Serializer\SerializationContext;

class TopController extends FOSRestController
{
    /**
     * Fetch Top-rated Anime.
     *
     * Gets a list of the top-rated anime on MyAnimeList. The get variable "page" is used
     * to select the set of results to return (in a default of 30 items per set). An
     * invalid or missing value defaults to page 1. The get variable "type" is used to
     * select the type of anime you want to see and can be one of "tv", "movie", "ova", or
     * "special". A missing or invalid value defaults to show all types.
     *
     * @param string  $apiVersion The API version of the request
     * @param Request $request    Contains all the needed information to get the list.
     *
     * @return View
     */
    public function getTopAnimeAction($apiVersion, Request $request)
    {
        // http://myanimelist.net/topanime.php?type=#{type}&limit=#{0}

        $page = (int) $request->query->get('page');

        if ($page <= 0) {
            $page = 1;
        }

        switch ($request->query->get('type')) {
            case 'tv':
            case 'movie':
            case 'ova':
            case 'special':
                $type = $request->query->get('type');
                break;
            default:
                $type = '';
                break;
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $animecontent = $downloader->fetch('/topanime.php?type='.$type.'&limit='.(($page * 50) - 50));
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        $etag = 'anime/top?page='.urlencode($page);
        if ($type) {
            $etag = $etag.'&amp;type='.urlencode($type);
        }

        $response = new Response();
        $serializationContext = SerializationContext::create();
        $serializationContext->setVersion($apiVersion);

        //For compatibility, API 1.0 explicitly passes null parameters.
        if ($apiVersion == '1.0') {
            $serializationContext->setSerializeNull(true);
        }

        $response->setPublic();
        $response->setMaxAge(10800); //Three hours
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag($etag);

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+10800 seconds'); //Three hours
        $response->setExpires($date);

        if (strpos($animecontent, 'No anime titles') !== false) {
            $view = $this->view(array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {
            $topanime = Top::parse($animecontent, 'anime');

            $view = $this->view($topanime);

            $view->setSerializationContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Fetch Top-rated Manga.
     *
     * Gets a list of the top-rated manga on MyAnimeList. The get variable "page" is used
     * to select the set of results to return (in a default of 30 items per set). An
     * invalid or missing value defaults to page 1. The get variable "type" is used to
     * select the type of manga you want to see and can be one of "manga", "novels",
     * "oneshots", "doujin", "manwha", "manhua", or "oels". A missing or invalid value
     * defaults to show all types.
     *
     * @param string  $apiVersion The API version of the request
     * @param Request $request    Contains all the needed information to get the list.
     *
     * @return View
     */
    public function getTopMangaAction($apiVersion, Request $request)
    {
        // http://myanimelist.net/topmanga.php?type=&limit=#{0}

        $page = (int) $request->query->get('page');

        if ($page <= 0) {
            $page = 1;
        }

        switch ($request->query->get('type')) {
            case 'manga':
            case 'novels':
            case 'oneshots':
            case 'doujin':
            case 'manwha':
            case 'manhua':
            case 'oels':
                $type = $request->query->get('type');
                break;
            default:
                $type = '';
                break;
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $mangacontent = $downloader->fetch('/topmanga.php?type='.$type.'&limit='.(($page * 50) - 50));
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        $etag = 'manga/top?page='.urlencode($page);
        if ($type) {
            $etag = $etag.'&amp;type='.urlencode($type);
        }

        $response = new Response();
        $serializationContext = SerializationContext::create();
        $serializationContext->setVersion($apiVersion);

        //For compatibility, API 1.0 explicitly passes null parameters.
        if ($apiVersion == '1.0') {
            $serializationContext->setSerializeNull(true);
        }

        $response->setPublic();
        $response->setMaxAge(10800); //Three hours
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag($etag);

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+10800 seconds'); //Three hours
        $response->setExpires($date);

        if (strpos($mangacontent, 'No manga titles') !== false) {
            $view = $this->view(array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {
            $topmanga = Top::parse($mangacontent, 'manga');

            $view = $this->view($topmanga);

            $view->setSerializationContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Fetch Top-rated Anime by Popularity.
     *
     * Gets a list of the top-rated anime on MyAnimeList sorted by popularity. The get
     * variable "page" is used to select the set of results to return (in a default of 30
     * items per set). An invalid or missing value defaults to page 1.
     *
     * @param string  $apiVersion The API version of the request
     * @param Request $request    Contains all the needed information to get the list.
     *
     * @return View
     */
    public function getPopularAnimeAction($apiVersion, Request $request)
    {
        // http://myanimelist.net/topanime.php?type=bypopularity&limit=#{0}

        $page = (int) $request->query->get('page');

        if ($page <= 0) {
            $page = 1;
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $animecontent = $downloader->fetch('/topanime.php?type=bypopularity&limit='.(($page * 50) - 50));
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        $response = new Response();
        $serializationContext = SerializationContext::create();
        $serializationContext->setVersion($apiVersion);

        //For compatibility, API 1.0 explicitly passes null parameters.
        if ($apiVersion == '1.0') {
            $serializationContext->setSerializeNull(true);
        }

        $response->setPublic();
        $response->setMaxAge(10800); //Three hours
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag('anime/popular?page='.urlencode($page));

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+10800 seconds'); //Three hours
        $response->setExpires($date);

        if (strpos($animecontent, 'No anime titles') !== false) {
            $view = $this->view(array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {
            $popularanime = Top::parse($animecontent, 'anime');

            $view = $this->view($popularanime);

            $view->setSerializationContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Fetch Top-rated Manga by Popularity.
     *
     * Gets a list of the top-rated manga on MyAnimeList sorted by popularity. The get
     * variable "page" is used to select the set of results to return (in a default of 30
     * items per set). An invalid or missing value defaults to page 1.
     *
     * @param string  $apiVersion The API version of the request
     * @param Request $request    Contains all the needed information to get the list.
     *
     * @return View
     */
    public function getPopularMangaAction($apiVersion, Request $request)
    {
        // http://myanimelist.net/topmanga.php?type=bypopularity&limit=#{0}

        $page = (int) $request->query->get('page');

        if ($page <= 0) {
            $page = 1;
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $mangacontent = $downloader->fetch('/topmanga.php?type=bypopularity&limit='.(($page * 50) - 50));
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        $response = new Response();
        $serializationContext = SerializationContext::create();
        $serializationContext->setVersion($apiVersion);

        //For compatibility, API 1.0 explicitly passes null parameters.
        if ($apiVersion == '1.0') {
            $serializationContext->setSerializeNull(true);
        }

        $response->setPublic();
        $response->setMaxAge(10800); //Three hours
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag('manga/popular?page='.urlencode($page));

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+10800 seconds'); //Three hours
        $response->setExpires($date);

        if (strpos($mangacontent, 'No manga titles') !== false) {
            $view = $this->view(array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {
            $popularmanga = Top::parse($mangacontent, 'manga');

            $view = $this->view($popularmanga);

            $view->setSerializationContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }
}
