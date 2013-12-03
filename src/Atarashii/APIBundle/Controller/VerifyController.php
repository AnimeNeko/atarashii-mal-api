<?php
namespace Atarashii\APIBundle\Controller;

use Guzzle\Http\Client;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

class verifyController extends FOSRestController
{

    /**
     * Verify credentials action
     * $username username
	 * $password password
     */
	public function VerifyAction(){
		#http://http://myanimelist.net/api/account/verify_credentials.xml

		//get the credentials we received
		$username = $this->getRequest()->server->get('PHP_AUTH_USER');
		$password = $this->getRequest()->server->get('PHP_AUTH_PW');

		//Don't bother making a request if the user didn't send any authentication
		if($username == null) {
			return $this->view(Array('error' => 'unauthorized'), 401);
		}

		$client = new Client('http://myanimelist.net');
		$client->setUserAgent('Atarashii');

		$request = $client->get('/api/account/verify_credentials.xml');
		$request->setAuth($username, $password);

		// Verify and send the request.
		try {
			$response = $request->send();
			return $this->view(Array('authorized' => 'OK'), 200);
		}
		catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
			return $this->view(Array('error' => 'unauthorized'), 401);
		}
	}
}