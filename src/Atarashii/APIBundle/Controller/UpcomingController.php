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

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Atarashii\APIBundle\Parser\Upcoming;

class UpcomingController extends FOSRestController
{
     /*
     * Upcoming get action
     * @return array
     *
     * @Rest\View()
     */
    public function getAnimeUpcomingAction(Request $request)
    {
        #http://myanimelist.net/anime.php?sd=#{day}&sm=#{month}&sy=#{year}&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show=#{page}

        $page = (int) $request->query->get('page');

        if ($page <= 0) {
            $page = 1;
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $animecontent = $downloader->fetch('/anime.php?sd='.date("j").'&sm='.date("n").'&sy='.date("y").'&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show='.(($page*20)-20));
        } catch (\Guzzle\Http\Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
        }

        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(10800); //Three hours
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag('anime/upcoming/?page=' . urlencode($page));

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+10800 seconds'); //Three hours
        $response->setExpires($date);

        if (strpos($animecontent,'No titles that matched') !== false) {
            $view = $this->view(Array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {
            $Upcominganime = Upcoming::parse($animecontent,'anime');

            $view = $this->view($Upcominganime);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    public function getMangaUpcomingAction(Request $request)
    {
        #http://myanimelist.net/manga.php?sd=#{day}&sm=#{month}&sy=#{year}&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show=#{page}

        $page = (int) $request->query->get('page');

        if ($page <= 0) {
            $page = 1;
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $mangacontent = $downloader->fetch('/manga.php?sd='.date("j").'&sm='.date("n").'&sy='.date("y").'&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show='.(($page*20)-20));
        } catch (\Guzzle\Http\Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
        }

        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(10800); //Three hours
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag('manga/upcoming/?page=' . urlencode($page));

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+10800 seconds'); //Three hours
        $response->setExpires($date);

         if (strpos($mangacontent,'No titles that matched') !== false) {
            $view = $this->view(Array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {
            $Upcomingmanga = Upcoming::parse($mangacontent,'manga');

            $view = $this->view($Upcomingmanga);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

     /*
     * Justadded get action
     * @return array
     *
     * @Rest\View()
     */
    public function getAnimeJustaddedAction(Request $request)
    {
        #http://myanimelist.net/anime.php?o=9&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=2&w=1&show=#{page}

        $page = (int) $request->query->get('page');

        if ($page <= 0) {
            $page = 1;
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $animecontent = $downloader->fetch('/anime.php?o=9&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=2&w=1&show='.(($page*20)-20));
        } catch (\Guzzle\Http\Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
        }

        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(10800); //Three hours
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag('manga/just_added/?page=' . urlencode($page));

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+10800 seconds'); //Three hours
        $response->setExpires($date);

          if (strpos($animecontent,'No titles that matched') !== false) {
            $view = $this->view(Array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {
            $Justaddedanime = Upcoming::parse($animecontent,'anime');

            $view = $this->view($Justaddedanime);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    public function getMangaJustaddedAction(Request $request)
    {
        #http://myanimelist.net/anime.php?o=9&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=2&w=1&show=#{page}

        $page = (int) $request->query->get('page');

        if ($page <= 0) {
            $page = 1;
        }

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $mangacontent = $downloader->fetch('/manga.php?o=9&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=2&w=1&show='.(($page*20)-20));
        } catch (\Guzzle\Http\Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
        }

        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(10800); //Three hours
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setEtag('manga/just_added/?page=' . urlencode($page));

        //Also, set "expires" header for caches that don't understand Cache-Control
        $date = new \DateTime();
        $date->modify('+10800 seconds'); //Three hours
        $response->setExpires($date);

         if (strpos($mangacontent,'No titles that matched') !== false) {
            $view = $this->view(Array('error' => 'not-found'));
            $view->setResponse($response);
            $view->setStatusCode(404);

            return $view;
        } else {
            $Justaddedmanga = Upcoming::parse($mangacontent,'manga');

            $view = $this->view($Justaddedmanga);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }
}
