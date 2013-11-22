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

		$downloader = $this->get('atarashii_api.downloader');
		$profilecontent = $downloader->fetch('/profile/' . $username);

		if (strpos($profilecontent,'Failed to find') !== false){
			$userprofile = 'Failed to find the specified user, please try again.';
		}else{
			$userprofile = User::parse($profilecontent);
		}
 		return $userprofile;
	}

	public function getFriendsAction($username)
	{
		#http://myanimelist.net/profile/#{username}/friends

		$downloader = $this->get('atarashii_api.downloader');
		$friendscontent = $downloader->fetch('/profile/' . $username . '/friends');

		if (strpos($friendscontent,'Failed to find') !== false){
			$friendlist = 'Failed to find the specified user, please try again.';
		}else{
			$friendlist = User::parseFriends($friendscontent);
		}
 		return $friendlist;
	}

}