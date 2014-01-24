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
use Symfony\Component\HttpFoundation\Request;
use Atarashii\APIBundle\Model\Anime;

use \SimpleXMLElement;

class AnimeListController extends FOSRestController
{

    /**
    * Get the list of anime stored for a user
    *
    * @param string $username The MyAnimeList username of the user whose list you want.
    *
    * @return View
    */
    public function getAction($username)
    {
        #http://myanimelist.net/malappinfo.php?u=#{username}&status=all&type=anime

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $animelistcontent = $downloader->fetch('/malappinfo.php?u=' . $username . '&status=all&type=anime');
        } catch (\Guzzle\Http\Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
        }

        if (strpos($animelistcontent, 'Invalid username') !== false) {
            $animelist = 'Failed to find the specified user, please try again.';
        } else {
            $animelistxml = new SimpleXMLElement($animelistcontent);
            $alist = array();

            $i = 0;
            foreach ($animelistxml->anime as $anime) {
                $alist[$i] = new Anime();
                $alist[$i]->id = (int) $anime->series_animedb_id;
                $alist[$i]->title = (string) $anime->series_title;
                $alist[$i]->setType((int) $anime->series_type);
                $alist[$i]->setStatus((int) $anime->series_status);
                $alist[$i]->episodes = (int) $anime->series_episodes;
                $alist[$i]->image_url = (string) $anime->series_image;
                $alist[$i]->listed_anime_id = (int) $anime->my_id;
                $alist[$i]->watched_episodes = (int) $anime->my_watched_episodes;
                $alist[$i]->score = (int) $anime->my_score;
                $alist[$i]->setWatchedStatus((int) $anime->my_status);
                $i++;
            }

            $animelist['statistics']['days'] = (float) $animelistxml->myinfo->user_days_spent_watching;
            $animelist['anime'] = $alist;
        }

         return $this->view($animelist);
    }

    /**
    * Add an anime to a user's list.
    *
    * Uses the contents of the HTTP Request to get the needed data for adding a title.
    * The user must have passed the basic authentication needs and the PHP_AUTH_USER and
    * PHP_AUTH_PW variables must be set. If so, the get variables of "anime_id", "status",
    * "episodes", and "score" are checked and used in the creation of an Anime object. The
    * object is used to make an XML document that is then posted to MyAnimeList.
    *
    * @param Request $request Contains all the needed information to add the title.
    *
    * @return View
    */
    public function addAction(Request $request)
    {
        #http://myanimelist.net/api/animelist/add/#{id}.xml

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        //Don't bother making a request if the user didn't send any authentication
        if ($username == null) {
            $view = $this->view(Array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        }

        $anime = new Anime();
        $anime->id = $request->request->get('anime_id');
        $anime->setWatchedStatus($request->request->get('status'));
        $anime->watched_episodes = $request->request->get('episodes');
        $anime->score = $request->request->get('score');

        $xmlcontent = $anime->MALApiXml();

        $connection = $this->get('atarashii_api.communicator');

        try {
            $result = $connection->sendXML('/api/animelist/add/' . $anime->id . '.xml', $xmlcontent, $username, $password);

            return $this->view('ok', 201);
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            $view = $this->view(Array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        } catch (\Guzzle\Http\Exception\ServerErrorResponseException $e) {
            return $this->view(Array('error' => 'not-found'), 404);
        } catch (\Guzzle\Http\Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
        }

    }

    /**
    * Update an anime on a user's list.
    *
    * Uses the contents of the HTTP Request to get the needed data for updating the
    * requested title. The user must have passed the basic authentication needs and the
    * PHP_AUTH_USER and PHP_AUTH_PW variables must be set. If so, the get variables of
    * "status", "episodes", and "score" are checked and used in the creation of an Anime
    * object. The object is used to make an XML document that is then posted to MyAnimeList.
    *
    * @param Request $request Contains all the needed information to update the title.
    * @param int     $id      ID of the anime.
    *
    * @return View
    */
    public function updateAction(Request $request, $id)
    {
        #http://myanimelist.net/api/animelist/update/#{id}.xml

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        //Don't bother making a request if the user didn't send any authentication
        if ($username == null) {
            $view = $this->view(Array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        }

        $anime = new Anime();
        $anime->id = $id;
        $anime->setWatchedStatus($request->request->get('status'));
        $anime->watched_episodes = $request->request->get('episodes');
        $anime->score = $request->request->get('score');

        $xmlcontent = $anime->MALApiXml();

        $connection = $this->get('atarashii_api.communicator');

        try {
            $result = $connection->sendXML('/api/animelist/update/' . $anime->id . '.xml', $xmlcontent, $username, $password);

            return $this->view('ok', 200);
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            $view = $this->view(Array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        } catch (\Guzzle\Http\Exception\ServerErrorResponseException $e) {
            return $this->view(Array('error' => 'not-found'), 404);
        } catch (\Guzzle\Http\Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
        }

    }

    /**
    * Delete an anime from a user's list.
    *
    * Uses the contents of the HTTP Request to get the needed data for deleting the
    * requested title. The user must have passed the basic authentication needs and the
    * PHP_AUTH_USER and PHP_AUTH_PW variables must be set. If so, an empty document is
    * then posted to MyAnimeList at the right URL to delete an item.
    *
    * @param Request $request Contains all the needed information to delete the title.
    * @param int     $id      ID of the anime.
    *
    * @return View
    */
    public function deleteAction(Request $request, $id)
    {
        #http://myanimelist.net/api/animelist/delete/#{id}.xml

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        //Don't bother making a request if the user didn't send any authentication
        if ($username == null) {
            $view = $this->view(Array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        }

        $connection = $this->get('atarashii_api.communicator');

        try {
            $result = $connection->sendXML('/api/animelist/delete/' . $id . '.xml', '', $username, $password);

            return $this->view('ok', 200);
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            $view = $this->view(Array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        } catch (\Guzzle\Http\Exception\ServerErrorResponseException $e) {
            return $this->view(Array('error' => 'not-found'), 404);
        } catch (\Guzzle\Http\Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
        }
    }
}
