<?php
namespace Atarashii\APIBundle\Service;

use Guzzle\Http\Client;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;

class Communicator {
	private $useragent;
	private $baseurl;
	private $client;
	private $cookies;

	function __construct($baseurl, $ua) {
		$this->useragent = $ua;
		$this->cookies = new CookiePlugin(new ArrayCookieJar());

		// create http client instance
		$this->client = new Client($baseurl);
		$this->client->addSubscriber($this->cookies);
	}

	public function cookieLogin($username, $password) {
		// create a request
		$request = $this->client->post("/login.php");
		$request->setHeader('User-Agent', $this->useragent);

		//Add our data transmission - MAL requires the XML content to be in a variable named "data"
		$request->setPostField('username', $username);
		$request->setPostField('password', $password);
		$request->setPostField('sublogin', ' Login ');

		// send request
		$request->send();
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