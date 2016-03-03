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
use Atarashii\APIBundle\Model\Anime;
use JMS\Serializer\SerializationContext;
use DateTime;
use SimpleXMLElement;

class AnimeListController extends FOSRestController
{
    /**
     * Get the list of anime stored for a user.
     *
     * @param string $username   The MyAnimeList username of the user whose list you want.
     * @param string $apiVersion The API version of the request
     *
     * @return View
     */
    public function getAction($username, $apiVersion)
    {
        // http://myanimelist.net/malappinfo.php?u=#{username}&status=all&type=anime

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $animelistcontent = $downloader->fetch('/malappinfo.php?u='.$username.'&status=all&type=anime');
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        if (strpos($animelistcontent, 'Invalid username') !== false) {
            return $this->view(array('error' => 'Failed to find the specified user, please try again.'), 200);
        } else {
            $animelistxml = new SimpleXMLElement($animelistcontent);
            $alist = array();

            $i = 0;
            foreach ($animelistxml->anime as $anime) {
                $alist[$i] = new Anime();
                $alist[$i]->setId((int) $anime->series_animedb_id);
                $alist[$i]->setTitle((string) $anime->series_title);
                $alist[$i]->setType((int) $anime->series_type);
                $alist[$i]->setStatus((int) $anime->series_status);
                $alist[$i]->setEpisodes((int) $anime->series_episodes);
                $alist[$i]->setImageUrl((string) $anime->series_image);
                $alist[$i]->setListedAnimeId((int) $anime->my_id);
                $alist[$i]->setWatchedEpisodes((int) $anime->my_watched_episodes);
                $alist[$i]->setScore((int) $anime->my_score);
                $alist[$i]->setWatchedStatus((int) $anime->my_status);
                ++$i;
            }

            $animelist['statistics']['days'] = (float) $animelistxml->myinfo->user_days_spent_watching;
            $animelist['anime'] = $alist;
        }

        $response = new Response();
        $serializationContext = SerializationContext::create();
        $serializationContext->setVersion($apiVersion);

        //For compatibility, API 1.0 explicitly passes null parameters.
        if ($apiVersion == '1.0') {
            $serializationContext->setSerializeNull(true);
        }

        $view = $this->view($animelist);

        $view->setSerializationContext($serializationContext);
        $view->setResponse($response);
        $view->setStatusCode(200);

        return $view;
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
        // http://myanimelist.net/api/animelist/add/#{id}.xml

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        //Don't bother making a request if the user didn't send any authentication
        if ($username === null) {
            $view = $this->view(array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        }

        $anime = new Anime();
        $anime->setId($request->request->get('anime_id'));

        //Only use values we were sent for the Update XML
        $update_items = array();
        try {
            if ($request->request->get('status') !== null) {
                $anime->setWatchedStatus($request->request->get('status'));
                $update_items[] = 'status';
            }

            if ($request->request->get('episodes') !== null) {
                $anime->setWatchedEpisodes($request->request->get('episodes'));
                $update_items[] = 'episodes';
            }

            if ($request->request->get('score') !== null) {
                $anime->setScore($request->request->get('score'));
                $update_items[] = 'score';
            }
        } catch (\Exception $e) {
            return $this->view(array('error' => $e->getMessage()), 500);
        }

        $xmlcontent = $anime->MALApiXml($update_items);

        $connection = $this->get('atarashii_api.communicator');

        try {
            $connection->sendXML('/api/animelist/add/'.$anime->getId().'.xml', $xmlcontent, $username, $password);

            return $this->view('ok', 201);
        } catch (Exception\ClientErrorResponseException $e) {
            $view = $this->view(array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        } catch (Exception\ServerErrorResponseException $e) {
            //MAL broke API responses, so we have to check the content on the response to make sure
            //it actually was an error.
            $response = $e->getResponse()->getBody(true);

            if (stripos($response, '201 Created') !== false) {
                return $this->view('ok', 200);
            }

            return $this->view(array('error' => 'not-found'), 404);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
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
     * @param Request $request    Contains all the needed information to update the title.
     * @param int     $id         ID of the anime.
     * @param float   $apiVersion The API version for the request
     *
     * @return View
     */
    public function updateAction(Request $request, $id, $apiVersion)
    {
        // http://myanimelist.net/api/animelist/update/#{id}.xml

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        //Don't bother making a request if the user didn't send any authentication
        if ($username === null) {
            $view = $this->view(array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        }

        $anime = new Anime();

        $anime->setId($id);

        //Only use values we were sent for the Update XML
        $update_items = array();
        try {
            if ($request->request->get('status') !== null) {
                $anime->setWatchedStatus($request->request->get('status'));
                $update_items[] = 'status';
            }

            if ($request->request->get('episodes') !== null) {
                $anime->setWatchedEpisodes($request->request->get('episodes'));
                $update_items[] = 'episodes';
            }

            if ($request->request->get('score') !== null) {
                $anime->setScore($request->request->get('score'));
                $update_items[] = 'score';
            }

            //API 2 Items
            if ($apiVersion >= 2.0) {
                if ($request->request->get('start') !== null) {
                    $anime->setWatchingStart(DateTime::createFromFormat('Y-m-d', $request->request->get('start'))); //Needs to be DT!
                    $update_items[] = 'start';
                }

                if ($request->request->get('end') !== null) {
                    $anime->setWatchingEnd(DateTime::createFromFormat('Y-m-d', $request->request->get('end'))); //Needs to be DT!
                    $update_items[] = 'end';
                }

                if ($request->request->get('downloaded_eps') !== null) {
                    $anime->setEpsDownloaded($request->request->get('downloaded_eps')); //Int
                    $update_items[] = 'downloaded';
                }

                if ($request->request->get('storage_type') !== null) {
                    $anime->setStorage($request->request->get('storage_type')); //Int (see getStorage mappings)
                    $update_items[] = 'storage';
                }

                if ($request->request->get('storage_amt') !== null) {
                    $anime->setStorageValue($request->request->get('storage_amt')); //Float, either in number of discs or in GB
                    $update_items[] = 'storageAmt';
                }

                if ($request->request->get('priority') !== null) {
                    $anime->setPriority($request->request->get('priority'));
                    $update_items[] = 'priority';
                }

                if ($request->request->get('rewatch_value') !== null) {
                    $anime->setRewatchValue($request->request->get('rewatch_value')); //Int
                    $update_items[] = 'rewatchValue';
                }

                if ($request->request->get('tags') !== null) {
                    $anime->setPersonalTags($request->request->get('tags')); //Comma-separated string
                    $update_items[] = 'tags';
                }

                if ($request->request->get('comments') !== null) {
                    $anime->setPersonalComments($request->request->get('comments')); //Plain text string. No HTML.
                    $update_items[] = 'comments';
                }

                if ($request->request->get('fansubber') !== null) {
                    $anime->setFansubGroup($request->request->get('fansubber')); //Plain string
                    $update_items[] = 'fansubber';
                }

                if ($request->request->get('is_rewatching') !== null) {
                    $anime->setRewatching($request->request->get('is_rewatching')); //Bool - 0 = no, 1 = yes
                    $update_items[] = 'isRewatching';
                }

                if ($request->request->get('rewatch_count') !== null) {
                    $anime->setRewatchCount($request->request->get('rewatch_count')); //Int
                    $update_items[] = 'rewatchCount';
                }
            }
        } catch (\Exception $e) {
            return $this->view(array('error' => $e->getMessage()), 500);
        }

        $xmlcontent = $anime->MALApiXml($update_items);

        $connection = $this->get('atarashii_api.communicator');

        try {
            $connection->sendXML('/api/animelist/update/'.$anime->getId().'.xml', $xmlcontent, $username, $password);

            return $this->view('ok', 200);
        } catch (Exception\ClientErrorResponseException $e) {
            $view = $this->view(array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        } catch (Exception\ServerErrorResponseException $e) {

            //MAL broke API responses, so we have to check the content on the response to make sure
            //it actually was an error.
            $response = $e->getResponse()->getBody(true);

            if (stripos($response, 'Updated') === 0) {
                return $this->view('ok', 200);
            }

            return $this->view(array('error' => 'not-found'), 404);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
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
     * @param int $id ID of the anime.
     *
     * @return View
     */
    public function deleteAction($id)
    {
        // http://myanimelist.net/api/animelist/delete/#{id}.xml

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        //Don't bother making a request if the user didn't send any authentication
        if ($username === null) {
            $view = $this->view(array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        }

        $connection = $this->get('atarashii_api.communicator');

        try {
            $connection->sendXML('/api/animelist/delete/'.$id.'.xml', '', $username, $password);

            return $this->view('ok', 200);
        } catch (Exception\ClientErrorResponseException $e) {
            $view = $this->view(array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        } catch (Exception\ServerErrorResponseException $e) {
            //MAL broke API responses, so we have to check the content on the response to make sure
            //it actually was an error.
            $response = $e->getResponse()->getBody(true);

            if (stripos($response, 'Deleted') === 0) {
                return $this->view('ok', 200);
            }

            return $this->view(array('error' => 'not-found'), 404);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }
    }
}
