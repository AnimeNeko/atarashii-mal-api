<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Atarashii\APIBundle\Model\Search;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

use \SimpleXMLElement;
use \DateTime;

class SearchController extends FOSRestController
{

	/**
     * Search results
     * @var string $page text to search 
     * @return array
     */
	public function getAnimeAction(Request $request)
	{
		#http://myanimelist.net/anime.php?c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&q=#{name}

		$page = $request->query->get('q');
		
		$downloader = $this->get('atarashii_api.downloader');
		$animecontent = $downloader->fetch('/anime.php?c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&q='.$page);

 		$searchanime = new Search();
  		$searchanime = $searchanime->parse($animecontent,'anime');
		
 		return $searchanime;
	}
	
	public function getMangaAction(Request $request)
	{
		#http://myanimelist.net/manga.php?c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&q=#{name}
		
		$page = $request->query->get('q');
		
		$downloader = $this->get('atarashii_api.downloader');
		$mangacontent = $downloader->fetch('/manga.php?c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&q='.$page);

 		$searchmanga = new Search();
  		$searchmanga = $searchmanga->parse($mangacontent,'manga');

 		return $searchmanga;
	}
}