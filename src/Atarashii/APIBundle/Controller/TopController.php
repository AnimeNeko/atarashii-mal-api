<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Atarashii\APIBundle\Model\top;

use \SimpleXMLElement;

class TopController extends FOSRestController
{

    /**
     * Profile get action
     * @var string $page page number(start at 1) 
     * @return array
     *
     * @Rest\View()
     */
	public function getAnimeAction($page)
	{
		#http://myanimelist.net/topanime.php?type=&limit=#{0}

		$downloader = $this->get('atarashii_api.downloader');
		$animecontent = $downloader->fetch('/topanime.php?type=&limit='.(($page*30)-30));

 		$topanime = new top();
  		$topanime = $topanime->parse($animecontent,'anime');
 		return $topanime;
	}
	
	public function getMangaAction($page)
	{
		#http://myanimelist.net/topmanga.php?type=&limit=#{0}
		
		$downloader = $this->get('atarashii_api.downloader');
		$mangacontent = $downloader->fetch('/topmanga.php?type=&limit='.(($page*30)-30));

 		$topmanga = new top();
  		$topmanga = $topmanga->parse($mangacontent,'manga');

 		return $topmanga;
	}

}