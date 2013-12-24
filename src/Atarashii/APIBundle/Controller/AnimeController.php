<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Atarashii\APIBundle\Parser\AnimeParser;

class AnimeController extends FOSRestController
{
    /**
     * Anime get action
     * @var integer $id Id of the anime
     * @return array
     *
     * @Rest\View()
     */
	public function getAction($id, Request $request)
	{
		#http://myanimelist.net/anime/#{id}

		$usepersonal = (int) $request->query->get('mine');

		$downloader = $this->get('atarashii_api.communicator');

		if($usepersonal) {
			//get the credentials we received
			$username = $this->getRequest()->server->get('PHP_AUTH_USER');
			$password = $this->getRequest()->server->get('PHP_AUTH_PW');

			//Don't bother making a request if the user didn't send any authentication
			if($username == null) {
				$view = $this->view(Array('error' => 'unauthorized'), 401);
				$view->setHeader('WWW-Authenticate', 'Basic realm="myanimelist.net"');
				return $view;
			}

			$downloader->cookieLogin($username, $password);
		}

		$animedetails = $downloader->fetch('/anime/' . $id);

		if (strpos($animedetails,'No series found') !== false){
			return $this->view(Array('error' => 'No series found, check the series id and try again.'), 404);
		}else{
			$anime = AnimeParser::parse($animedetails);
			return $anime;
		}
	}
}