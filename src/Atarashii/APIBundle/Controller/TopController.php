<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Atarashii\APIBundle\Parser\Top;

use \SimpleXMLElement;

class TopController extends FOSRestController
{

	public function getTopAnimeAction(Request $request)
	{
		#http://myanimelist.net/topanime.php?type=&limit=#{0}

		$page = (int) $request->query->get('page');

		if ($page <= 0)	{
			$page = 1;
		}

		$downloader = $this->get('atarashii_api.communicator');

		try {
			$animecontent = $downloader->fetch('/topanime.php?type=&limit='.(($page*30)-30));
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

		if (strpos($animecontent,'No anime titles') !== false){
			return $this->view(Array('error' => 'not-found'), 404);
		}else{
			$topanime = Top::parse($animecontent,'anime');;
			return $topanime;
		}
	}

	public function getTopMangaAction(Request $request)
	{
		#http://myanimelist.net/topmanga.php?type=&limit=#{0}

		$page = (int) $request->query->get('page');

		if ($page <= 0)	{
			$page = 1;
		}

		$downloader = $this->get('atarashii_api.communicator');

		try {
			$mangacontent = $downloader->fetch('/topmanga.php?type=&limit='.(($page*30)-30));
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

 		if (strpos($mangacontent,'No manga titles') !== false){
			return $this->view(Array('error' => 'not-found'), 404);
		}else{
			$topmanga = Top::parse($mangacontent,'manga');
			return $topmanga;
		}
	}

	public function getPopularAnimeAction(Request $request)
	{
		#http://myanimelist.net/topanime.php?type=bypopularity&limit=#{0}

		$page = (int) $request->query->get('page');

		if ($page <= 0)	{
			$page = 1;
		}

		$downloader = $this->get('atarashii_api.communicator');

		try {
			$animecontent = $downloader->fetch('/topanime.php?type=bypopularity&limit='.(($page*30)-30));
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

		if (strpos($animecontent,'No anime titles') !== false){
			return $this->view(Array('error' => 'not-found'), 404);
		}else{
			$popularanime = Top::parse($animecontent,'anime');
			return $popularanime;
		}
	}

	public function getPopularMangaAction(Request $request)
	{
		#http://myanimelist.net/topmanga.php?type=bypopularity&limit=#{0}

		$page = (int) $request->query->get('page');

		if ($page <= 0)	{
			$page = 1;
		}

		$downloader = $this->get('atarashii_api.communicator');

		try {
			$mangacontent = $downloader->fetch('/topmanga.php?type=bypopularity&limit='.(($page*30)-30));
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

 		if (strpos($mangacontent,'No manga titles') !== false){
			return $this->view(Array('error' => 'not-found'), 404);
		}else{
			$popularmanga = Top::parse($mangacontent,'manga');
			return $popularmanga;
		}
	}
}