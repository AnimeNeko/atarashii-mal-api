<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Atarashii\APIBundle\Parser\Justadded;

use \SimpleXMLElement;

class JustaddedController extends FOSRestController
{

    /**
     * Justadded get action
     * @return array
     *
     * @Rest\View()
     */
	public function getAnimeAction(Request $request)
	{
		#http://myanimelist.net/anime.php?o=9&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=2&w=1&show=#{page}

		$page = $request->query->get('page');

		$downloader = $this->get('atarashii_api.downloader');
		$animecontent = $downloader->fetch('/anime.php?o=9&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=2&w=1&show='.(($page*20)-20));

 		$Justaddedanime = new Justadded();
  		$Justaddedanime = $Justaddedanime->parse($animecontent,'anime');
 		return $Justaddedanime;
	}

	public function getMangaAction(Request $request)
	{
		$page = $request->query->get('page');

		#http://myanimelist.net/anime.php?o=9&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=2&w=1&show=#{page}

		$downloader = $this->get('atarashii_api.downloader');
		$mangacontent = $downloader->fetch('/manga.php?o=9&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=2&w=1&show='.(($page*20)-20));

 		$Justaddedmanga = new Justadded();
  		$Justaddedmanga = $Justaddedmanga->parse($mangacontent,'manga');

 		return $Justaddedmanga;
	}

}