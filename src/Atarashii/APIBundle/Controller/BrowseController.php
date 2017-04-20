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

use Atarashii\APIBundle\Model\Anime;
use Atarashii\APIBundle\Model\Manga;
use Atarashii\APIBundle\Parser\TitleParser;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Exception;
use Atarashii\APIBundle\Parser\Upcoming;
use FOS\RestBundle\Context\Context;

class BrowseController extends FOSRestController
{
    /**
     * @param         $apiVersion  The API version of the request
     * @param         $requestType The anime or manga request string
     * @param Request $request     HTTP Request object
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getBrowseAction($apiVersion, $requestType, Request $request)
    {
        $downloader = $this->get('atarashii_api.communicator');

        $page = (int) $request->query->get('page');
        if ($page <= 0) {
            $page = 1;
        }

        // Create URL parts supported by MAL
        $pagePart = '&show='.(($page * 50) - 50);

        $keyword = '&q='.$request->query->get('keyword');
        $score = '&score='.((int) $request->query->get('score'));
        $reverse = '&w='.((int) $request->query->get('reverse'));
        $rating = '&r='.((int) $request->query->get('rating'));
        $genreType = '&gx='.((int) $request->query->get('genre_type'));
        $status = '&status='.$this->getStatusId($request->query->get('status'));
        $endDateArray = explode('-', $request->query->get('end_date'));
        if (count($endDateArray) == 3) {
            $endDate = '&ey='.$endDateArray[0].'&em='.$endDateArray[1].'&ed='.$endDateArray[2];
        } else {
            $endDate = '';
        }
        $startDateArray = explode('-', $request->query->get('start_date'));
        if (count($startDateArray) == 3) {
            $startDate = '&sy='.$startDateArray[0].'&sm='.$startDateArray[1].'&sd='.$startDateArray[2];
        } else {
            $startDate = '';
        }

        if ($requestType === 'anime') {
            $type = '&type='.Anime::getTypeId($request->query->get('type'));
            $genres = Anime::getGenresId($request->query->get('genres'));
            $sort = '&o='.Anime::getColumnId($request->query->get('sort'), $requestType);
        } else {
            $type = '&type='.Manga::getTypeId($request->query->get('type'));
            $genres = Manga::getGenresId($request->query->get('genres'));
            $sort = '&o='.Manga::getColumnId($request->query->get('sort'), $requestType);
        }

        // Combine all URL parts for the request
        $url = $genres.$sort.$reverse.$endDate.$startDate.$rating.$status.$type.$keyword.$score.$genreType.$pagePart;

        try {
            $content = $downloader->fetch('/'.$requestType.'.php?c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&c[]=g'.$url);
        } catch (Exception\ServerException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        } catch (Exception\ClientException $e) {
            $content = $e->getResponse()->getBody();
        }

        $response = new Response();
        $serializationContext = new Context();
        $serializationContext->setVersion($apiVersion);

        // MAL does contain a bug where excluded genres allow the same amount of pages as normal without warning
        // To avoid issues we check if the page number does match the content page number.
        preg_match('/>\[(\d+?)\]/', $content, $matches);

        if ((strpos($content, 'No titles that matched') !== false) || (strpos($content, 'This page doesn\'t exist') !== false)) {
            return $this->view(array('error' => 'not-found'), 404);
        } elseif (count($matches) > 1 && (int) $matches[1] !== $page) {
            return $this->view(array(), 200);
        } else {
            //MAL now returns 404 on a single result. Workaround
            if (method_exists($content, 'getStatusCode') && $content->getStatusCode() === 404) {
                $location = $content->getHeader('Location');

                try {
                    $content = $downloader->fetch($location);
                    if ($requestType === 'anime') {
                        $searchResult = array(TitleParser::parse($content, $apiVersion, 'anime'));
                    } else {
                        $searchResult = array(TitleParser::parse($content, $apiVersion, 'manga'));
                    }
                } catch (Exception\ServerException $e) {
                    return $this->view(array('error' => 'network-error'), 500);
                }
            } else {
                if ($downloader->wasRedirected()) {
                    if ($requestType === 'anime') {
                        $searchResult = array(TitleParser::parse($content, $apiVersion, 'anime'));
                    } else {
                        $searchResult = array(TitleParser::parse($content, $apiVersion, 'manga'));
                    }
                } else {
                    $searchResult = Upcoming::parse($content, $requestType);
                }
            }

            $response->setPublic();
            $response->setMaxAge(86400); //One day
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->setEtag(md5(serialize($searchResult)));

            //Also, set "expires" header for caches that don't understand Cache-Control
            $date = new \DateTime();
            $date->modify('+86400 seconds'); //One day
            $response->setExpires($date);

            $view = $this->view($searchResult);

            $view->setContext($serializationContext);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Returns the right status ID.
     *
     * @param $status The parameter with the status type
     *
     * @return int The statusId supported by MAL
     */
    private function getStatusId($status)
    {
        switch (strtolower($status)) {
            case 1:
            case 'airing':
            case 'publishing':
                return 1;
            case 2:
            case 'finished':
                return 2;
            case 3:
            case 'not yet':
                return 3;
            default:
                return 0;
        }
    }
}
