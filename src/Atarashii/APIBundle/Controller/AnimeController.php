<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Atarashii\APIBundle\Parser\AnimeParser;

//Temporary for offline testing
use Symfony\Component\Finder\Finder;

class AnimeController extends FOSRestController
{
    /**
     * Anime get action
     * @var integer $id Id of the anime
     * @return array
     *
     * @Rest\View()
     */
	public function getAction($id)
	{
		#http://myanimelist.net/anime/#{id}

		$downloader = $this->get('atarashii_api.downloader');
		$animedetails = $downloader->fetch('/anime/' . $id);

 		$anime = AnimeParser::parse($animedetails);

 		return $anime;
	}
}