<?php
namespace Atarashii\APIBundle\Service;

use Guzzle\Http\Client;

class Downloader {
	private $useragent;
	private $baseurl;
	private $client;

	function __construct($baseurl, $ua) {
		$this->useragent = $ua;

		// create http client instance
		$this->client = new Client($baseurl);
	}

	public function fetch($url) {

		// create a request
		$request = $this->client->get($url);
		$request->setHeader('User-Agent', $this->useragent);

		// send request / get response
		$response = $request->send();

		// this is the response body from the requested page
		return $response->getBody();
	}
}