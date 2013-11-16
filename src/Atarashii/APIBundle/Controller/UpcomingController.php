<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Atarashii\APIBundle\Model\Upcoming;

use \SimpleXMLElement;

class UpcomingController extends FOSRestController
{

    /**
     * Upcoming get action
     * @return array
     *
     * @Rest\View()
     */
	public function getAnimeAction(Request $request)
	{
		#http://myanimelist.net/anime.php?sd=#{day}&sm=#{month}&sy=#{year}&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show=#{page}

		$page = $request->query->get('page');
		
		$downloader = $this->get('atarashii_api.downloader');
		$animecontent = $downloader->fetch('/anime.php?sd='.date("j").'&sm='.date("n").'&sy='.date("y").'&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show='.(($page*20)-20));
		echo $animecontent;
 		$Upcominganime = new Upcoming();
  		$Upcominganime = $Upcominganime->parse($animecontent,'anime');
 		return $Upcominganime;
	}
	
	public function getMangaAction(Request $request)
	{
		$page = $request->query->get('page');
		
		#http://myanimelist.net/manga.php?sd=#{day}&sm=#{month}&sy=#{year}&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show=#{page}
		
		$downloader = $this->get('atarashii_api.downloader');
		$mangacontent = $downloader->fetch('/manga.php?sd='.date("j").'&sm='.date("n").'&sy='.date("y").'&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show='.(($page*20)-20));

 		$Upcomingmanga = new Upcoming();
  		$Upcomingmanga = $Upcomingmanga->parse($mangacontent,'manga');

 		return $Upcomingmanga;
	}

}