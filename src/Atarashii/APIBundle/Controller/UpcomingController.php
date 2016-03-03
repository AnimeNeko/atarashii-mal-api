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
use Atarashii\APIBundle\Parser\Upcoming;
use JMS\Serializer\SerializationContext;

class UpcomingController extends FOSRestController
{
    /**
     * Fetch Upcoming Anime.
     *
     * Gets the list of anime that will be beginning to air in the future. The get variable
     * "page" is used to select the set of results to return (in a default of 30 items per
     * set). An invalid or missing value defaults to page 1. The get variable "start_date"
     * is used to select the date you want the list to begin. A missing or invalid value
     * defaults to the current date.
     *
     * @param string  $apiVersion The API version of the request
     * @param Request $request    Contains all the needed information to get the list.
     *
     * @return View
     */
    public function getAnimeUpcomingAction($apiVersion, Request $request)
    {
        // http://myanimelist.net/anime.php?sd=#{day}&sm=#{month}&sy=#{year}&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show=#{page}

        $page = (int) $request->query->get('page');
        $start = (int) $request->query->get('start_date');

        if ($page <= 0) {
            $page = 1;
        }

        if (strlen($start) != 8) {
            $startDate = new \DateTime();
        } else {
            $startDate = new \DateTime($start);
        }

        $startYear = $startDate->format('Y');
        $startMonth = $startDate->format('m');
        $startDay = $startDate->format('d');

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $animecontent = $downloader->fetch('/anime.php?sd='.$startDay.'&sm='.$startMonth.'&sy='.$startYear.'&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show='.(($page * 50) - 50));
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
        $response->setEtag('anime/upcoming?page='.urlencode($page).'&amp;start_date='.urlencode($startYear.$startMonth.$startDay));

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+10800 seconds'); //Three hours
        $response->setExpires($date);

        if (strpos($animecontent, 'No titles that matched') !== false) {
            $view = $this->view(array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {
            $Upcominganime = Upcoming::parse($animecontent, 'anime');

            $view = $this->view($Upcominganime);

            $view->setSerializationContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Fetch Upcoming Manga.
     *
     * Gets the list of manga that will be starting publication in the future. The get
     * variable "page" is used to select the set of results to return (in a default of 30
     * items per set). An invalid or missing value defaults to page 1. The get variable
     * "start_date" is used to select the date you want the list to begin. A missing or
     * invalid value defaults to the current date.
     *
     * @param string  $apiVersion The API version of the request
     * @param Request $request    Contains all the needed information to get the list.
     *
     * @return View
     */
    public function getMangaUpcomingAction($apiVersion, Request $request)
    {
        // http://myanimelist.net/manga.php?sd=#{day}&sm=#{month}&sy=#{year}&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show=#{page}

        $page = (int) $request->query->get('page');
        $start = (int) $request->query->get('start_date');

        if ($page <= 0) {
            $page = 1;
        }

        if (strlen($start) != 8) {
            $startDate = new \DateTime();
        } else {
            $startDate = new \DateTime($start);
        }

        $startYear = $startDate->format('Y');
        $startMonth = $startDate->format('m');
        $startDay = $startDate->format('d');

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $mangacontent = $downloader->fetch('/manga.php?sd='.$startDay.'&sm='.$startMonth.'&sy='.$startYear.'&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show='.(($page * 50) - 50));
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
        $response->setEtag('manga/upcoming?page='.urlencode($page).'&amp;start_date='.urlencode($startYear.$startMonth.$startDay));

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+10800 seconds'); //Three hours
        $response->setExpires($date);

        if (strpos($mangacontent, 'No titles that matched') !== false) {
            $view = $this->view(array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {
            $Upcomingmanga = Upcoming::parse($mangacontent, 'manga');

            $view = $this->view($Upcomingmanga);

            $view->setSerializationContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Fetch Anime Recently Added to MAL.
     *
     * Gets a list of anime that were recently added to the MyAnimeList website. The get
     * variable "page" is used to select the set of results to return (in a default of 30
     * items per set). An invalid or missing value defaults to page 1.
     *
     * @param string  $apiVersion The API version of the request
     * @param Request $request    Contains all the needed information to get the list.
     *
     * @return View
     */
    public function getAnimeJustaddedAction($apiVersion, Request $request)
    {
        // http://myanimelist.net/anime.php?o=9&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=2&w=1&show=#{page}

        $page = (int) $request->query->get('page');

        if ($page <= 0) {
            $page = 1;
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $animecontent = $downloader->fetch('/anime.php?o=9&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=2&w=1&show='.(($page * 20) - 20));
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
        $response->setEtag('manga/just_added?page='.urlencode($page));

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+10800 seconds'); //Three hours
        $response->setExpires($date);

        if (strpos($animecontent, 'No titles that matched') !== false) {
            $view = $this->view(array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {
            $Justaddedanime = Upcoming::parse($animecontent, 'anime');

            $view = $this->view($Justaddedanime);

            $view->setSerializationContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Fetch Manga Recently Added to MAL.
     *
     * Gets a list of manga that were recently added to the MyAnimeList website. The get
     * variable "page" is used to select the set of results to return (in a default of 30
     * items per set). An invalid or missing value defaults to page 1.
     *
     * @param string  $apiVersion The API version of the request
     * @param Request $request    Contains all the needed information to get the list.
     *
     * @return View
     */
    public function getMangaJustaddedAction($apiVersion, Request $request)
    {
        // http://myanimelist.net/anime.php?o=9&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=2&w=1&show=#{page}

        $page = (int) $request->query->get('page');

        if ($page <= 0) {
            $page = 1;
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $mangacontent = $downloader->fetch('/manga.php?o=9&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=2&w=1&show='.(($page * 20) - 20));
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
        $response->setEtag('manga/just_added?page='.urlencode($page));

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+10800 seconds'); //Three hours
        $response->setExpires($date);

        if (strpos($mangacontent, 'No titles that matched') !== false) {
            $view = $this->view(array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {
            $Justaddedmanga = Upcoming::parse($mangacontent, 'manga');

            $view = $this->view($Justaddedmanga);

            $view->setSerializationContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }
}
