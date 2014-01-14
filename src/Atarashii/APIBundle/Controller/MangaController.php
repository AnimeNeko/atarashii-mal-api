<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Atarashii\APIBundle\Parser\MangaParser;

class MangaController extends FOSRestController
{
    /**
     * Manga get action
     * @var integer $id Id of the manga
     * @return array
     *
     * @Rest\View()
     */
    public function getAction($id, Request $request)
    {
        #http://myanimelist.net/manga/#{id}

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
            } catch (\Guzzle\Http\Exception\CurlException $e) {
                return $this->view(Array('error' => 'network-error'), 500);
            }
        }

        try {
            $mangadetails = $downloader->fetch('/manga/' . $id);
        } catch (\Guzzle\Http\Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
        }

        if (strpos($mangadetails,'No manga found') !== false) {
            return $this->view(Array('error' => 'No manga found, check the manga id and try again.'), 404);
        } else {
            $manga = MangaParser::parse($mangadetails);

            $response = new Response();

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
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }
}
