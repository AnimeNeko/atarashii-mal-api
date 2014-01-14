<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use Atarashii\APIBundle\Parser\User;

class UserController extends FOSRestController
{

    /**
     * Profile get action
     * @var string $username username
     * @return array
     *
     * @Rest\View()
     */
	public function getProfileAction($username)
	{
		#http://myanimelist.net/profile/#{username}

		$downloader = $this->get('atarashii_api.communicator');

		try {
			$profilecontent = $downloader->fetch('/profile/' . $username);
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

		$response = new Response();
		$response->setPublic();
		$response->setMaxAge(900); //15 minutes
		$response->headers->addCacheControlDirective('must-revalidate', true);
		$response->setEtag('profile/' . $username);

		//Also, set "expires" header for caches that don't understand Cache-Control
		$date = new \DateTime();
		$date->modify('+900 seconds'); //15 minutes
		$response->setExpires($date);

		if (strpos($profilecontent,'Failed to find') !== false) {
			$view = $this->view(Array('error' => 'not-found'));
			$view->setResponse($response);
			$view->setStatusCode(404);
			return $view;
		} else {
			$userprofile = User::parse($profilecontent);

			$view = $this->view($userprofile);
			$view->setResponse($response);
			$view->setStatusCode(200);
			return $view;
		}
	}

	public function getFriendsAction($username)
	{
		#http://myanimelist.net/profile/#{username}/friends

		$downloader = $this->get('atarashii_api.communicator');

		try {
			$friendscontent = $downloader->fetch('/profile/' . $username . '/friends');
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

		$response = new Response();
		$response->setPublic();
		$response->setMaxAge(900); //15 minutes
		$response->headers->addCacheControlDirective('must-revalidate', true);
		$response->setEtag('friends/' . $username);

		//Also, set "expires" header for caches that don't understand Cache-Control
		$date = new \DateTime();
		$date->modify('+900 seconds'); //15 minutes
		$response->setExpires($date);

		if (strpos($friendscontent,'Failed to find') !== false) {
			$view = $this->view(Array('error' => 'not-found'));
			$view->setResponse($response);
			$view->setStatusCode(404);
			return $view;
		} else {
			$friendlist = User::parseFriends($friendscontent);

			$view = $this->view($friendlist);
			$view->setResponse($response);
			$view->setStatusCode(200);
			return $view;
		}
	}
}
