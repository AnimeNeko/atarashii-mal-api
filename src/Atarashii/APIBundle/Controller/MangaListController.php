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
use Atarashii\APIBundle\Model\Manga;

use \SimpleXMLElement;

class MangaListController extends FOSRestController
{
    /**
    * Get the list of manga stored for a user
    *
    * @param string $username The MyAnimeList username of the user whose list you want.
    *
    * @return View
    */
    public function getAction($username)
    {
        // http://myanimelist.net/malappinfo.php?u=#{username}&status=all&type=manga

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $mangalistcontent = $downloader->fetch('/malappinfo.php?u=' . $username . '&status=all&type=manga');
        } catch (\Guzzle\Http\Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
        }

        if (strpos($mangalistcontent, 'Invalid username') !== false) {
            $mangalist = 'Failed to find the specified user, please try again.';
        } else {
            $mangalistxml = new SimpleXMLElement($mangalistcontent);
            $mlist = array();

            $i = 0;
            foreach ($mangalistxml->manga as $manga) {
                $mlist[$i] = new Manga();
                $mlist[$i]->id = (int) $manga->series_mangadb_id;
                $mlist[$i]->title = (string) $manga->series_title;
                $mlist[$i]->setType((int) $manga->series_type);
                $mlist[$i]->setStatus((int) $manga->series_status);
                $mlist[$i]->chapters = (int) $manga->series_chapters;
                $mlist[$i]->volumes = (int) $manga->series_volumes;
                $mlist[$i]->image_url = (string) $manga->series_image;
                $mlist[$i]->listed_manga_id = (int) $manga->my_id;
                $mlist[$i]->volumes_read = (int) $manga->my_read_volumes;
                $mlist[$i]->chapters_read = (int) $manga->my_read_chapters;
                $mlist[$i]->score = (int) $manga->my_score;
                $mlist[$i]->setReadStatus($manga->my_status);
                $i++;
            }

            $mangalist['statistics']['days'] = (float) $mangalistxml->myinfo->user_days_spent_watching;
            $mangalist['manga'] = $mlist;
        }

         return $this->view($mangalist);
    }

    /**
    * Add a manga to a user's list.
    *
    * Uses the contents of the HTTP Request to get the needed data for adding a title.
    * The user must have passed the basic authentication needs and the PHP_AUTH_USER and
    * PHP_AUTH_PW variables must be set. If so, the get variables of "manga_id", "status",
    * "chapters", "volumes", and "score" are checked and used in the creation of a manga
    * object. The object is used to make an XML document that is then posted to MyAnimeList.
    *
    * @param Request $request Contains all the needed information to add the title.
    *
    * @return View
    */
    public function addAction(Request $request)
    {
        // http://mymangalist.net/api/mangalist/add/#{id}.xml

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        //Don't bother making a request if the user didn't send any authentication
        if ($username == null) {
            $view = $this->view(Array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        }

        $manga = new Manga();
        $manga->id = $request->request->get('manga_id');
        $manga->read_status = $request->request->get('status');
        $manga->chapters_read = $request->request->get('chapters');
        $manga->volumes_read = $request->request->get('volumes');
        $manga->score = $request->request->get('score');

        $xmlcontent = $manga->MALApiXml();

        $connection = $this->get('atarashii_api.communicator');

        try {
            $result = $connection->sendXML('/api/mangalist/add/' . $manga->id . '.xml', $xmlcontent, $username, $password);

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
    * Update a manga on a user's list.
    *
    * Uses the contents of the HTTP Request to get the needed data for updating the
    * requested title. The user must have passed the basic authentication needs and the
    * PHP_AUTH_USER and PHP_AUTH_PW variables must be set. If so, the get variables of
    * "status", "chapters", "volumes", and "score" are checked and used in the creation
    * of a manga object. The object is used to make an XML document that is then posted
    * to MyAnimeList.
    *
    * @param Request $request Contains all the needed information to update the title.
    * @param int     $id      ID of the manga.
    *
    * @return View
    */
    public function updateAction(Request $request, $id)
    {
        // http://mymangalist.net/api/mangalist/update/#{id}.xml

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        //Don't bother making a request if the user didn't send any authentication
        if ($username == null) {
            $view = $this->view(Array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        }

        $manga = new Manga();
        $manga->id = $id;
        $manga->read_status = $request->request->get('status');
        $manga->chapters_read = $request->request->get('chapters');
        $manga->volumes_read = $request->request->get('volumes');
        $manga->score = $request->request->get('score');

        $xmlcontent = $manga->MALApiXml();

        $connection = $this->get('atarashii_api.communicator');

        try {
            $result = $connection->sendXML('/api/mangalist/update/' . $manga->id . '.xml', $xmlcontent, $username, $password);

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
    * Delete a manga from a user's list.
    *
    * Uses the contents of the HTTP Request to get the needed data for deleting the
    * requested title. The user must have passed the basic authentication needs and the
    * PHP_AUTH_USER and PHP_AUTH_PW variables must be set. If so, an empty document is
    * then posted to MyAnimeList at the right URL to delete an item.
    *
    * @param Request $request Contains all the needed information to delete the title.
    * @param int     $id      ID of the manga.
    *
    * @return View
    */
    public function deleteAction(Request $request, $id)
    {
        // http://mymangalist.net/api/mangalist/delete/#{id}.xml

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
            $result = $connection->sendXML('/api/mangalist/delete/' . $id . '.xml', '', $username, $password);

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
