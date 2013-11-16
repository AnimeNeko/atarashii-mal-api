<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Atarashii\APIBundle\Model\Popular;

use \SimpleXMLElement;

class PopularController extends FOSRestController
{

    /**
     * Profile get action
     * @return array
     *
     * @Rest\View()
     */
	public function getAnimeAction(Request $request)
	{
		#http://myanimelist.net/topanime.php?type=bypopularity&limit=#{0}

		$page = $request->query->get('page');
		
		$downloader = $this->get('atarashii_api.downloader');
		$animecontent = $downloader->fetch('/topanime.php?type=bypopularity&limit='.(($page*30)-30));

 		$popularanime = new Popular();
  		$popularanime = $popularanime->parse($animecontent,'anime');
 		return $popularanime;
	}
	
	public function getMangaAction(Request $request)
	{
		$page = $request->query->get('page');
		
		#http://myanimelist.net/topmanga.php?type=bypopularity&limit=#{0}
		
		$downloader = $this->get('atarashii_api.downloader');
		$mangacontent = $downloader->fetch('/topmanga.php?type=bypopularity&limit='.(($page*30)-30));

 		$popularmanga = new Popular();
  		$popularmanga = $popularmanga->parse($mangacontent,'manga');

 		return $popularmanga;
	}

}