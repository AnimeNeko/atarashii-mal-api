<?php
namespace Atarashii\APIBundle\Service;

use Guzzle\Http\Client;

class Communicator {
	private $useragent;
	private $baseurl;
	private $client;

	function __construct($baseurl, $ua) {
		$this->useragent = $ua;

		// create http client instance
		$this->client = new Client($baseurl);
	}

	public function fetch($url, $username = null, $password = null) {

		// create a request
		$request = $this->client->get($url);
		$request->setHeader('User-Agent', $this->useragent);

		if($username) {
			$request->setAuth($username, $password);
		}

		// send request / get response
		$response = $request->send();

		// this is the response body from the requested page
		return $response->getBody();
	}

	public function sendXML($url, $content, $username = null, $password = null) {

		// create a request
		$request = $this->client->post($url);
		$request->setHeader('User-Agent', $this->useragent);

		if($username) {
			$request->setAuth($username, $password);
		}

		//Add our data transmission - MAL requires the XML content to be in a variable named "data"
		$request->setPostField('data', $content);

		// send request / get response
		$response = $request->send();

		// this is the response body from the requested page
		return $response->getBody();
	}

}