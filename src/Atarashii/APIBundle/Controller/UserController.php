<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Atarashii\APIBundle\Model\User;

use \SimpleXMLElement;

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
			$userprofile = new User();
			$userprofile->parse($profilecontent);
		}
 		return $userprofile;
	}

}