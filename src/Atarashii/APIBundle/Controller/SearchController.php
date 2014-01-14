<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Atarashii\APIBundle\Parser\Upcoming;
use Atarashii\APIBundle\Parser\AnimeParser;
use Atarashii\APIBundle\Parser\MangaParser;

use \DateTime;

class SearchController extends FOSRestController
{
     /**
     * Search results
     * $page pagenumber (default 1)
     * $query keyword to search
     *
     * @return array
     */
	public function getAnimeAction(Request $request)
	{
		#http://myanimelist.net/anime.php?c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&q=#{name}&show=#{page}

		$page = (int) $request->query->get('page',1);
                $query = $request->query->get('q');

		$downloader = $this->get('atarashii_api.communicator');
		try {
			$animecontent = $downloader->fetch('/anime.php?c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&q='.$query.'&show='.(($page*20)-20));
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

		$response = new Response();
		$response->setPublic();
		$response->setMaxAge(3600); //One hour
		$response->headers->addCacheControlDirective('must-revalidate', true);
		$response->setEtag('anime/search/?q=' . urlencode($query));

		//Also, set "expires" header for caches that don't understand Cache-Control
		$date = new \DateTime();
		$date->modify('+3600 seconds'); //One hour
		$response->setExpires($date);

		if (strpos($animecontent,'No titles that matched') !== false) {
			$view = $this->view(Array('error' => 'not-found'));
			$view->setResponse($response);
			$view->setStatusCode(404);
			return $view;
		} else {
			if ($downloader->wasRedirected()) {
				$searchanime = Array(AnimeParser::parse($animecontent));
			} else {
				$searchanime = Upcoming::parse($animecontent,'anime');
			}

			$view = $this->view($searchanime);
			$view->setResponse($response);
			$view->setStatusCode(200);
			return $view;
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

		$response = new Response();
		$response->setPublic();
		$response->setMaxAge(3600); //One hour
		$response->headers->addCacheControlDirective('must-revalidate', true);
		$response->setEtag('manga/search/?q=' . urlencode($query));

		//Also, set "expires" header for caches that don't understand Cache-Control
		$date = new \DateTime();
		$date->modify('+3600 seconds'); //One hour
		$response->setExpires($date);

		if (strpos($mangacontent,'No titles that matched') !== false) {
			$view = $this->view(Array('error' => 'not-found'));
			$view->setResponse($response);
			$view->setStatusCode(404);
			return $view;
		} else {
			if ($downloader->wasRedirected()) {
				$searchmanga = Array(MangaParser::parse($mangacontent));
			} else {
				$searchmanga = Upcoming::parse($mangacontent,'manga');
			}

			$view = $this->view($searchmanga);
			$view->setResponse($response);
			$view->setStatusCode(200);
			return $view;
		}
	}
}
