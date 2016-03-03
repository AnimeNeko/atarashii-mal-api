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
use Atarashii\APIBundle\Model\Manga;
use JMS\Serializer\SerializationContext;
use DateTime;
use SimpleXMLElement;

class MangaListController extends FOSRestController
{
    /**
     * Get the list of manga stored for a user.
     *
     * @param string $username   The MyAnimeList username of the user whose list you want.
     * @param string $apiVersion The API version of the request
     *
     * @return View
     */
    public function getAction($username, $apiVersion)
    {
        // http://myanimelist.net/malappinfo.php?u=#{username}&status=all&type=manga

        $downloader = $this->get('atarashii_api.communicator');

        try {
            $mangalistcontent = $downloader->fetch('/malappinfo.php?u='.$username.'&status=all&type=manga');
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        if (strpos($mangalistcontent, 'Invalid username') !== false) {
            return $this->view(array('error' => 'Failed to find the specified user, please try again.'), 200);
        } else {
            $mangalistxml = new SimpleXMLElement($mangalistcontent);
            $mlist = array();

            $i = 0;
            foreach ($mangalistxml->manga as $manga) {
                $mlist[$i] = new Manga();
                $mlist[$i]->setId((int) $manga->series_mangadb_id);
                $mlist[$i]->setTitle((string) $manga->series_title);
                $mlist[$i]->setType((int) $manga->series_type);
                $mlist[$i]->setStatus((int) $manga->series_status);
                $mlist[$i]->setChapters((int) $manga->series_chapters);
                $mlist[$i]->setVolumes((int) $manga->series_volumes);
                $mlist[$i]->setImageUrl((string) $manga->series_image);
                $mlist[$i]->setListedMangaId((int) $manga->my_id);
                $mlist[$i]->setVolumesRead((int) $manga->my_read_volumes);
                $mlist[$i]->setChaptersRead((int) $manga->my_read_chapters);
                $mlist[$i]->setScore((int) $manga->my_score);
                $mlist[$i]->setReadStatus($manga->my_status);
                ++$i;
            }

            $mangalist['statistics']['days'] = (float) $mangalistxml->myinfo->user_days_spent_watching;
            $mangalist['manga'] = $mlist;
        }

        $response = new Response();
        $serializationContext = SerializationContext::create();
        $serializationContext->setVersion($apiVersion);

        //For compatibility, API 1.0 explicitly passes null parameters.
        if ($apiVersion == '1.0') {
            $serializationContext->setSerializeNull(true);
        }

        $view = $this->view($mangalist);

        $view->setSerializationContext($serializationContext);
        $view->setResponse($response);
        $view->setStatusCode(200);

        return $view;
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
        if ($username === null) {
            $view = $this->view(array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        }

        $manga = new Manga();
        $manga->setId($request->request->get('manga_id'));

        //Only use values we were sent for the Update XML
        $update_items = array();

        try {
            if ($request->request->get('status') !== null) {
                $manga->setReadStatus($request->request->get('status'));
                $update_items[] = 'status';
            }

            if ($request->request->get('chapters') !== null) {
                $manga->setChaptersRead($request->request->get('chapters'));
                $update_items[] = 'chapters';
            }

            if ($request->request->get('volumes') !== null) {
                $manga->setVolumesRead($request->request->get('volumes'));
                $update_items[] = 'volumes';
            }

            if ($request->request->get('score') !== null) {
                $manga->setScore($request->request->get('score'));
                $update_items[] = 'score';
            }
        } catch (\Exception $e) {
            return $this->view(array('error' => $e->getMessage()), 500);
        }

        $xmlcontent = $manga->MALApiXml($update_items);

        $connection = $this->get('atarashii_api.communicator');

        try {
            $connection->sendXML('/api/mangalist/add/'.$manga->getId().'.xml', $xmlcontent, $username, $password);

            return $this->view('ok', 201);
        } catch (Exception\ClientErrorResponseException $e) {
            $view = $this->view(array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        } catch (Exception\ServerErrorResponseException $e) {
            //MAL broke API responses, so we have to check the content on the response to make sure
            //it actually was an error.
            $response = $e->getResponse()->getBody(true);

            if (preg_match('/^\d+?<!DOCTYPE/', $response) === 1) {
                return $this->view('ok', 200);
            } elseif (stripos($response, '201 Created') !== false) {
                return $this->view('ok', 200);
            }

            return $this->view(array('error' => 'not-found'), 404);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
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
    public function updateAction(Request $request, $id, $apiVersion)
    {
        // http://mymangalist.net/api/mangalist/update/#{id}.xml

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        //Don't bother making a request if the user didn't send any authentication
        if ($username === null) {
            $view = $this->view(array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        }

        $manga = new Manga();
        $manga->setId($id);

        //Only use values we were sent for the Update XML
        $update_items = array();

        try {
            if ($request->request->get('status') !== null) {
                $manga->setReadStatus($request->request->get('status'));
                $update_items[] = 'status';
            }

            if ($request->request->get('chapters') !== null) {
                $manga->setChaptersRead($request->request->get('chapters'));
                $update_items[] = 'chapters';
            }

            if ($request->request->get('volumes') !== null) {
                $manga->setVolumesRead($request->request->get('volumes'));
                $update_items[] = 'volumes';
            }

            if ($request->request->get('score') !== null) {
                $manga->setScore($request->request->get('score'));
                $update_items[] = 'score';
            }

            //API 2 Items
            if ($apiVersion >= 2.0) {
                if ($request->request->get('downloaded_chap') !== null) {
                    $manga->setChapDownloaded($request->request->get('downloaded_chap')); //Int
                    $update_items[] = 'downloaded';
                }

                if ($request->request->get('reread_count') !== null) {
                    $manga->setRereadCount($request->request->get('reread_count')); //Int
                    $update_items[] = 'rereadCount';
                }

                if ($request->request->get('reread_value') !== null) {
                    $manga->setRereadValue($request->request->get('reread_value')); //Int
                    $update_items[] = 'rereadValue';
                }

                if ($request->request->get('start') !== null) {
                    $manga->setReadingStart(DateTime::createFromFormat('Y-m-d', $request->request->get('start'))); //Needs to be DT!
                    $update_items[] = 'start';
                }

                if ($request->request->get('end') !== null) {
                    $manga->setReadingEnd(DateTime::createFromFormat('Y-m-d', $request->request->get('end'))); //Needs to be DT!
                    $update_items[] = 'end';
                }

                if ($request->request->get('priority') !== null) {
                    $manga->setPriority($request->request->get('priority'));
                    $update_items[] = 'priority';
                }

                if ($request->request->get('is_rereading') !== null) {
                    $manga->setRereading($request->request->get('is_rereading')); //Bool - 0 = no, 1 = yes
                    $update_items[] = 'isRereading';
                }

                if ($request->request->get('comments') !== null) {
                    $manga->setPersonalComments($request->request->get('comments')); //Plain text string. No HTML.
                    $update_items[] = 'comments';
                }

                if ($request->request->get('tags') !== null) {
                    $manga->setPersonalTags($request->request->get('tags')); //Comma-separated string
                    $update_items[] = 'tags';
                }
            }
        } catch (\Exception $e) {
            return $this->view(array('error' => $e->getMessage()), 500);
        }

        $xmlcontent = $manga->MALApiXml($update_items);

        $connection = $this->get('atarashii_api.communicator');

        try {
            $connection->sendXML('/api/mangalist/update/'.$manga->getId().'.xml', $xmlcontent, $username, $password);

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
        if ($username === null) {
            $view = $this->view(array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        }

        $connection = $this->get('atarashii_api.communicator');

        try {
            $connection->sendXML('/api/mangalist/delete/'.$id.'.xml', '', $username, $password);

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
