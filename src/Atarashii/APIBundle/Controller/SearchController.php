<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Atarashii\APIBundle\Parser\Upcoming;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

use \SimpleXMLElement;
use \DateTime;

class SearchController extends FOSRestController {

     /**
     * Search results
     * $page pagenumber (default 1)
     * $query keyword to search
     *
     * @return array
     */
	public function getAnimeAction(Request $request){
		#http://myanimelist.net/anime.php?c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&q=#{name}&show=#{page}

		$page = (int) $request->query->get('page',1);
                $query = $request->query->get('q');

		$downloader = $this->get('atarashii_api.communicator');
		try {
			$animecontent = $downloader->fetch('/anime.php?c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&q='.$query.'&show='.(($page*20)-20));
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

                if (strpos($animecontent,'No titles that matched') !== false){
                    return $this->view(Array('error' => 'No titles that matched your query were found.'), 404);
                }else{
                    $searchanime = Upcoming::parse($animecontent,'anime');
                    return $searchanime;
                }
	}

	public function getMangaAction(Request $request)
	{
		#http://myanimelist.net/manga.php?c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&q=#{name}&show=#{page}

		$page = (int) $request->query->get('page',1);
                $query = $request->query->get('q');

		$downloader = $this->get('atarashii_api.communicator');
		try {
			$mangacontent = $downloader->fetch('/manga.php?c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&q='.$query.'&show='.(($page*20)-20));
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

                if (strpos($mangacontent,'No titles that matched') !== false){
                    return $this->view(Array('error' => 'No titles that matched your query were found.'), 404);
                }else{
                    $searchmanga = Upcoming::parse($mangacontent,'manga');
                    return $searchmanga;
                }
	}
}