<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Atarashii\APIBundle\Parser\MangaParser;

class MangaController extends FOSRestController
{
    /**
     * Manga get action
     * @var integer $id Id of the manga
     * @return array
     *
     * @Rest\View()
     */
	public function getAction($id, Request $request)
	{
		#http://myanimelist.net/manga/#{id}

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

		$mangadetails = $downloader->fetch('/manga/' . $id);

 		$manga = MangaParser::parse($mangadetails);

 		return $manga;
	}
}