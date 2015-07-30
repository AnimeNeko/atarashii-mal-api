<?php
/**
* Atarashii MAL API
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014-2015 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/

namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Guzzle\Http\Exception;

class VerifyController extends FOSRestController
{

    /**
    * Verify a login works for MyAnimeList
    *
    * This call just mirrors the official API to check if a login is valid.
    * The user must have passed the basic authentication needs and the PHP_AUTH_USER and
    * PHP_AUTH_PW variables must be set. If not working, an HTTP code of 401 is returned,
    * while a valid login will cause a code of 200 to be returned.
    *
    * @return View
    */
    public function verifyAction()
    {
        // http://http://myanimelist.net/api/account/verify_credentials.xml

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
            $connection->fetch('/api/account/verify_credentials.xml', $username, $password);

            return $this->view(Array('authorized' => 'OK'), 200);
        } catch (Exception\ClientErrorResponseException $e) {
            $view = $this->view(Array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        } catch (Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
        }
    }
}
