<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Atarashii\APIBundle\Parser\Top;

class TopController extends FOSRestController
{
     /*
     * Top get action
     * @return array
     *
     * @Rest\View()
     */
	public function getTopAnimeAction(Request $request)
	{
		#http://myanimelist.net/topanime.php?type=&limit=#{0}

		$page = (int) $request->query->get('page');

		if ($page <= 0) {
			$page = 1;
		}

		$downloader = $this->get('atarashii_api.communicator');

		try {
			$animecontent = $downloader->fetch('/topanime.php?type=&limit='.(($page*30)-30));
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

		$response = new Response();
		$response->setPublic();
		$response->setMaxAge(10800); //Three hours
		$response->headers->addCacheControlDirective('must-revalidate', true);
		$response->setEtag('anime/top/?page=' . urlencode($page));

		//Also, set "expires" header for caches that don't understand Cache-Control
		$date = new \DateTime();
		$date->modify('+10800 seconds'); //Three hours
		$response->setExpires($date);

		if (strpos($animecontent,'No anime titles') !== false) {
			$view = $this->view(Array('error' => 'not-found'));
			$view->setResponse($response);
			$view->setStatusCode(404);
			return $view;
		} else {
			$topanime = Top::parse($animecontent,'anime');

			$view = $this->view($topanime);
			$view->setResponse($response);
			$view->setStatusCode(200);
			return $view;
		}
	}

	public function getTopMangaAction(Request $request)
	{
		#http://myanimelist.net/topmanga.php?type=&limit=#{0}

		$page = (int) $request->query->get('page');

		if ($page <= 0) {
			$page = 1;
		}

		$downloader = $this->get('atarashii_api.communicator');

		try {
			$mangacontent = $downloader->fetch('/topmanga.php?type=&limit='.(($page*30)-30));
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

		$response = new Response();
		$response->setPublic();
		$response->setMaxAge(10800); //Three hours
		$response->headers->addCacheControlDirective('must-revalidate', true);
		$response->setEtag('manga/top/?page=' . urlencode($page));

		//Also, set "expires" header for caches that don't understand Cache-Control
		$date = new \DateTime();
		$date->modify('+10800 seconds'); //Three hours
		$response->setExpires($date);

 		if (strpos($mangacontent,'No manga titles') !== false) {
			$view = $this->view(Array('error' => 'not-found'));
			$view->setResponse($response);
			$view->setStatusCode(404);
			return $view;
		} else {
			$topmanga = Top::parse($mangacontent,'manga');

			$view = $this->view($topmanga);
			$view->setResponse($response);
			$view->setStatusCode(200);
			return $view;
		}
	}

     /*
     * Popular get action
     * @return array
     *
     * @Rest\View()
     */
	public function getPopularAnimeAction(Request $request)
	{
		#http://myanimelist.net/topanime.php?type=bypopularity&limit=#{0}

		$page = (int) $request->query->get('page');

		if ($page <= 0) {
			$page = 1;
		}

		$downloader = $this->get('atarashii_api.communicator');

		try {
			$animecontent = $downloader->fetch('/topanime.php?type=bypopularity&limit='.(($page*30)-30));
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

		$response = new Response();
		$response->setPublic();
		$response->setMaxAge(10800); //Three hours
		$response->headers->addCacheControlDirective('must-revalidate', true);
		$response->setEtag('anime/popular/?page=' . urlencode($page));

		//Also, set "expires" header for caches that don't understand Cache-Control
		$date = new \DateTime();
		$date->modify('+10800 seconds'); //Three hours
		$response->setExpires($date);

		if (strpos($animecontent,'No anime titles') !== false) {
			$view = $this->view(Array('error' => 'not-found'));
			$view->setResponse($response);
			$view->setStatusCode(404);
			return $view;
		} else {
			$popularanime = Top::parse($animecontent,'anime');

			$view = $this->view($popularanime);
			$view->setResponse($response);
			$view->setStatusCode(200);
			return $view;
		}
	}

	public function getPopularMangaAction(Request $request)
	{
		#http://myanimelist.net/topmanga.php?type=bypopularity&limit=#{0}

		$page = (int) $request->query->get('page');

		if ($page <= 0) {
			$page = 1;
		}

		$downloader = $this->get('atarashii_api.communicator');

		try {
			$mangacontent = $downloader->fetch('/topmanga.php?type=bypopularity&limit='.(($page*30)-30));
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

		$response = new Response();
		$response->setPublic();
		$response->setMaxAge(10800); //Three hours
		$response->headers->addCacheControlDirective('must-revalidate', true);
		$response->setEtag('manga/popular/?page=' . urlencode($page));

		//Also, set "expires" header for caches that don't understand Cache-Control
		$date = new \DateTime();
		$date->modify('+10800 seconds'); //Three hours
		$response->setExpires($date);

 		if (strpos($mangacontent,'No manga titles') !== false) {
			$view = $this->view(Array('error' => 'not-found'));
			$view->setResponse($response);
			$view->setStatusCode(404);
			return $view;
		} else {
			$popularmanga = Top::parse($mangacontent,'manga');

			$view = $this->view($popularmanga);
			$view->setResponse($response);
			$view->setStatusCode(200);
			return $view;
		}
	}
}
