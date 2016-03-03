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
use Atarashii\APIBundle\Helper\Date;
use Atarashii\APIBundle\Parser\MessagesParser;

class MessagesController extends FOSRestController
{
    /**
     * Get the messages list.
     *
     * @param Request $request HTTP Request object
     *
     * @internal param int $page The pagenumber assigned by MAL.
     *
     * @return View
     */
    public function getAction(Request $request)
    {
        // http://myanimelist.net/mymessages.php?go=&show=#{page}

        $downloader = $this->get('atarashii_api.communicator');

        $page = (int) $request->query->get('page');
        if ($page <= 0) {
            $page = 1;
        }

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        try {
            if (!$downloader->cookieLogin($username, $password)) {
                $view = $this->view(array('error' => 'unauthorized'), 401);
                $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

                return $view;
            }
            Date::setTimeZone($downloader->fetch('/editprofile.php'));
            $messagesdetails = $downloader->fetch('/mymessages.php?go=&show='.(($page * 20) - 20));
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        if (strpos($messagesdetails, 'You have 0 messages') !== false) {
            return $this->view(array('error' => 'No messages found.'), 404);
        } else {
            $messages = MessagesParser::parse($messagesdetails);

            $response = new Response();

            $view = $this->view($messages);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Get the messages of the specified id.
     *
     * @param int $id The ID of the message
     *
     * @return View
     */
    public function getMessageAction($id)
    {
        // http://myanimelist.net/mymessages.php?go=read&id=#{id}

        $downloader = $this->get('atarashii_api.communicator');

        $id = (int) $id;
        if ($id <= 0) {
            return $this->view(array('error' => 'Invalid message ID'), 404);
        }

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        try {
            if (!$downloader->cookieLogin($username, $password)) {
                $view = $this->view(array('error' => 'unauthorized'), 401);
                $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

                return $view;
            }
            $messagesdetails = $downloader->fetch('/mymessages.php?go=read&id='.$id);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        if (strpos($messagesdetails, 'Invalid ID') !== false) {
            return $this->view(array('error' => 'Invalid message ID.'), 404);
        } else {
            $messages = messagesParser::parseMessage($messagesdetails, $id);

            $response = new Response();

            $view = $this->view($messages);
            $view->setResponse($response);
            $view->setStatusCode(200);

            return $view;
        }
    }

    /**
     * Get the messages of the specified username.
     *
     * @param int $id The action ID of a message
     *
     * @return View
     */
    public function deleteAction($id)
    {
        // http://myanimelist.net/mymessages.php?go=delete&id=#{id}

        $downloader = $this->get('atarashii_api.communicator');

        $id = (int) $id;
        if ($id <= 0) {
            return $this->view(array('error' => 'Invalid action ID'), 404);
        }

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        try {
            if (!$downloader->cookieLogin($username, $password)) {
                $view = $this->view(array('error' => 'unauthorized'), 401);
                $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

                return $view;
            }
            $messagesdetails = $downloader->fetch('http://myanimelist.net/mymessages.php?go=delete&id='.$id);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        if (strpos($messagesdetails, 'Invalid access') !== false) {
            return $this->view(array('error' => 'Invalid action ID.'), 404);
        } elseif (strpos($messagesdetails, 'Successfully deleted your message') !== false) {
            return $this->view(array('status' => 'OK'), 200);
        }
    }

    /**
     * Get the messages of the specified username.
     *
     * @param Request $request HTTP Request object
     *
     * @internal param String $subject Subject of the message
     * @internal param String $message The body of a message
     * @internal param int    $id The thread ID of a message
     *
     * @return View
     */
    public function sendAction(Request $request)
    {
        // http://myanimelist.net/mymessages.php?go=send&threadid=#{id}&toname=#{username}

        $downloader = $this->get('atarashii_api.communicator');

        $id = (int) $request->request->get('id');
        if ($id <= 0) {
            return $this->view(array('error' => 'Invalid thread ID'), 404);
        }

        $send_username = $request->request->get('username');
        $subject = $request->request->get('subject');
        $message = $request->request->get('message');

        //get the credentials we received
        $username = $this->getRequest()->server->get('PHP_AUTH_USER');
        $password = $this->getRequest()->server->get('PHP_AUTH_PW');

        try {
            if (!$downloader->cookieLogin($username, $password)) {
                $view = $this->view(array('error' => 'unauthorized'), 401);
                $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

                return $view;
            }
            $messagesdetails = $downloader->sendMessage('threadid='.$id.'&toname='.$send_username, $subject, $message);
        } catch (Exception\CurlException $e) {
            return $this->view(array('error' => 'network-error'), 500);
        }

        if (strpos($messagesdetails, 'Invalid input') !== false) {
            return $this->view(array('error' => 'Invalid username.'), 404);
        } elseif (strpos($messagesdetails, 'Successfully sent your PM') !== false) {
            return $this->view(array('status' => 'OK'), 200);
        } elseif (strpos($messagesdetails, 'only have 75 messages') !== false) {
            return $this->view(array('error' => 'Maximum inbox size reached'), 200);
        }
    }
}
