<?php
/**
 * Atarashii MAL API.
 *
 * @author    Kyle Lanchman <k.lanchman@gmail.com>
 * @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
 * @author    Michael Johnson <youngmug@animeneko.net>
 * @copyright 2014-2016 Ratan Dhawtal and Michael Johnson
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
 */

namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Exception;
use Atarashii\APIBundle\Parser\PersonParser;

class PersonController extends FOSRestController
{
    /**
     * Get the details of a person.
     *
     * @param int $id The ID of the person as assigned by MyAnimeList
     *
     * @return View
     */
    public function getAction($id)
    {
        // http://myanimelist.net/people/#{id}
        $downloader = $this->get('atarashii_api.communicator');

        try {
            $personDetails = $downloader->fetch('/people/'.$id);
        } catch (Exception\ServerException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        } catch (Exception\ClientException $e) {
            $personDetails = $e->getResponse()->getBody();
        }

        if (strpos($personDetails, 'This page doesn\'t exist') !== false) {
            return $this->view(array('error' => 'not-found'), 200);
        } else {
            $person = PersonParser::parse($personDetails);

            $response = new Response();
            $response->setPublic();
            $response->setMaxAge(86400); //One day
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->setEtag(md5(serialize($person)));

            //Also, set "expires" header for caches that don't understand Cache-Control
            $date = new \DateTime();
            $date->modify('+172800 seconds'); //two days
            $response->setExpires($date);

            $view = $this->view($person);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }
}
