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

use Atarashii\APIBundle\Parser\ForumParser;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Http\Exception;
use Atarashii\APIBundle\Parser\Upcoming;
use Atarashii\APIBundle\Parser\SearchParser;
use Atarashii\APIBundle\Parser\AnimeParser;
use Atarashii\APIBundle\Parser\MangaParser;
use JMS\Serializer\SerializationContext;

class SearchController extends FOSRestController
{
    /**
     * Search for an anime.
     *
     * @param string  $apiVersion The API version of the request
     * @param Request $request    The HTTP Request object.
     *
     * @return View
     */
    public function getAnimeAction($apiVersion, Request $request)
    {
        $page = (int) $request->query->get('page');
        $mine = (int) $request->query->get('mine');
        $query = $request->query->get('q');

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');

        //Only make an Official request if API 2.1+ is used and we got credentials
        if ($username === null || $username === '' || $apiVersion < '2.1' || $mine !== 1) {
            return $this->unOfficialSearch('anime', $page, $query, $apiVersion);
        } else {
            return $this->officialSearch('anime', $query, $username, $apiVersion);
        }
    }

    /**
     * Search for a manga.
     *
     * @param string  $apiVersion The API version of the request
     * @param Request $request    The HTTP Request object.
     *
     * @return View
     */
    public function getMangaAction($apiVersion, Request $request)
    {
        $page = (int) $request->query->get('page');
        $mine = (int) $request->query->get('mine');
        $query = $request->query->get('q');

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');

        //Only make an Official request if API 2.1+ is used and we got credentials
        if ($username === null || $username === '' || $apiVersion < '2.1' || $mine !== 1) {
            return $this->unOfficialSearch('manga', $page, $query, $apiVersion);
        } else {
            return $this->officialSearch('manga', $query, $username, $apiVersion);
        }
    }

    /**
     * Request search data using the Official.
     *
     * With the Official way the API will get the some other data.
     * Synonyms and english titles are provided together with a full synopsis.
     *
     * @param $type           The type is a string which can be 'anime' or 'manga'
     * @param $username       The username
     * @param $query          The title or keywords to seach for the record
     * @param $apiVersion     The API version which was used for the request
     *
     * @return \FOS\RestBundle\View\View
     */
    private function officialSearch($type, $query, $username, $apiVersion)
    {
        //http://myanimelist.net/api/manga/search.xml?q=full+metal
        //http://myanimelist.net/api/anime/search.xml?q=full+metal

        $downloader = $this->get('atarashii_api.communicator');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        try {
            $content = $downloader->fetch('/api/'.$type.'/search.xml?q='.$query, $username, $password);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        } catch (Exception\ClientErrorResponseException $e) {
            $content = $e->getResponse();
        }

        $result = SearchParser::parse($content, $type);

        $response = new Response();
        $serializationContext = SerializationContext::create();
        $serializationContext->setVersion($apiVersion);

        //Only include cache info if it doesn't include personal data.
        $response->setPublic();
        $response->setMaxAge(3600); //One hour
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag($type.'/search?q='.urlencode($query));

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+3600 seconds'); //One hour
        $response->setExpires($date);

        $view = $this->view($result);

        $view->setSerializationContext($serializationContext);
        $view->setResponse($response);
        $view->setStatusCode(200);

        return $view;
    }

    /**
     * Request search data using the unOfficial.
     *
     * With the unOfficial way the API will get the data from the search page instead of the official API.
     *
     * @param $type       The type is a string which can be 'anime' or 'manga'
     * @param $page       Integer which is used to get the desired page
     * @param $query      The title or keywords to seach for the record
     * @param $apiVersion The API version which was used for the request
     *
     * @return \FOS\RestBundle\View\View
     */
    private function unOfficialSearch($type, $page, $query, $apiVersion)
    {
        // http://myanimelist.net/anime.php?c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&q=#{name}&show=#{page}
        // http://myanimelist.net/manga.php?c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&q=#{name}&show=#{page}

        if ($page <= 0) {
            $page = 1;
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $content = $downloader->fetch('/'.$type.'.php?c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&q='.$query.'&show='.(($page * 50) - 50));
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        } catch (Exception\ClientErrorResponseException $e) {
            //MAL now returns 404 on searches without results.
            //We still need the content for logic purposes.
            $content = $e->getResponse();
        }

        $response = new Response();
        $serializationContext = SerializationContext::create();
        $serializationContext->setVersion($apiVersion);

        $response->setPublic();
        $response->setMaxAge(3600); //One hour
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag($type.'/search?q='.urlencode($query).'page='.$page);

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+3600 seconds'); //One hour
        $response->setExpires($date);

        if ((strpos($content, 'No titles that matched') !== false) || (strpos($content, 'This page doesn\'t exist') !== false)) {
            $view = $this->view(array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {

            //For compatibility, API 1.0 explicitly passes null parameters.
            if ($apiVersion == '1.0') {
                $serializationContext->setSerializeNull(true);
            }

            //MAL now returns 404 on a single result. Workaround
            if (method_exists($content, 'getStatusCode') && $content->getStatusCode() === 404) {
                $location = $content->getHeader('Location');

                try {
                    $content = $downloader->fetch($location);
                    if ($type === 'anime') {
                        $searchResult = array(AnimeParser::parse($content));
                    } else {
                        $searchResult = array(MangaParser::parse($content));
                    }
                } catch (Exception\CurlException $e) {
                    return $this->view(array('error' => 'network-error'), 500);
                }
            } else {
                if ($downloader->wasRedirected()) {
                    if ($type === 'anime') {
                        $searchResult = array(AnimeParser::parse($content));
                    } else {
                        $searchResult = array(MangaParser::parse($content));
                    }
                } else {
                    $searchResult = Upcoming::parse($content, $type);
                }
            }

            $view = $this->view($searchResult);

            $view->setSerializationContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Search for a topic in the forum.
     *
     * @param Request $request The HTTP Request object.
     *
     * @return View
     */
    public function getForumAction(Request $request)
    {
        // http://myanimelist.net/forum/?action=search&q=#{keyword}&u=#{user}&uloc=#{userCategory}&loc=#{category}

        $user = $request->query->get('user');
        $query = $request->query->get('query');
        $userCategory = (int) $request->query->get('userCategory');
        $category = (int) $request->query->get('category');

        if ($userCategory <= 0) {
            $userCategory = 1;
        }

        if ($category < 0) {
            $category = -1;
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $content = $downloader->fetch('/forum/?action=search&q='.$query.'&u='.$user.'&uloc='.$userCategory.'&loc='.$category);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        $response = new Response();

        $response->setPublic();
        $response->setMaxAge(3600); //One hour
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag('forum/search?q='.urlencode($query.$user));

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+3600 seconds'); //One hour
        $response->setExpires($date);

        if (strpos($content, 'User not found') !== false || !strpos($content, 'Topic') !== false) {
            $view = $this->view(array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {
            $result = ForumParser::parseTopics($content);

            $view = $this->view($result);

            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }
}
