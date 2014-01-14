<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;

class VerifyController extends FOSRestController
{

    /**
     * Verify credentials action
     * $username username
     * $password password
     */
    public function VerifyAction()
    {
        #http://http://myanimelist.net/api/account/verify_credentials.xml

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
            $response = $connection->fetch('/api/account/verify_credentials.xml', $username, $password);

            return $this->view(Array('authorized' => 'OK'), 200);
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            $view = $this->view(Array('error' => 'unauthorized'), 401);
            $view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');

            return $view;
        } catch (\Guzzle\Http\Exception\CurlException $e) {
            return $this->view(Array('error' => 'network-error'), 500);
        }
    }
}
