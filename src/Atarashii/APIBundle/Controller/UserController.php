<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
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

		if (strpos($profilecontent,'Failed to find') !== false){
			return $this->view(Array('error' => 'Failed to find the specified user, please try again.'), 404);
		}else{
			$userprofile = User::parse($profilecontent);
			return $userprofile;
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

		if (strpos($friendscontent,'Failed to find') !== false){
			return $this->view(Array('error' => 'Failed to find the specified user, please try again.'), 404);
		}else{
			$friendlist = User::parseFriends($friendscontent);
			return $friendlist;
		}
	}
}