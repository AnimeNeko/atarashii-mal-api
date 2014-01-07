<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Atarashii\APIBundle\Parser\Upcoming;

use \SimpleXMLElement;

class UpcomingController extends FOSRestController
{

    /**
     * Upcoming get action
     * @return array
     *
     * @Rest\View()
     */
	public function getAnimeUpcomingAction(Request $request)
	{
		#http://myanimelist.net/anime.php?sd=#{day}&sm=#{month}&sy=#{year}&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show=#{page}

		$page = (int) $request->query->get('page');

		if ($page <= 0)	{
			$page = 1;
		}

		$downloader = $this->get('atarashii_api.communicator');

		try {
			$animecontent = $downloader->fetch('/anime.php?sd='.date("j").'&sm='.date("n").'&sy='.date("y").'&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show='.(($page*20)-20));
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

		$Upcominganime = Upcoming::parse($animecontent,'anime');
 		return $Upcominganime;
	}

	public function getMangaUpcomingAction(Request $request)
	{
		#http://myanimelist.net/manga.php?sd=#{day}&sm=#{month}&sy=#{year}&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show=#{page}

		$page = (int) $request->query->get('page');

		if ($page <= 0)	{
			$page = 1;
		}

		$downloader = $this->get('atarashii_api.communicator');

		try {
			$mangacontent = $downloader->fetch('/manga.php?sd='.date("j").'&sm='.date("n").'&sy='.date("y").'&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show='.(($page*20)-20));
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

 		$Upcomingmanga = Upcoming::parse($mangacontent,'manga');

 		return $Upcomingmanga;
	}

	/**
     * Justadded get action
     * @return array
     *
     * @Rest\View()
     */
	public function getAnimeJustaddedAction(Request $request)
	{
		#http://myanimelist.net/anime.php?o=9&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=2&w=1&show=#{page}

		$page = (int) $request->query->get('page');

		if ($page <= 0){
			$page = 1;
		}

		$downloader = $this->get('atarashii_api.communicator');

		try {
			$animecontent = $downloader->fetch('/anime.php?o=9&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=2&w=1&show='.(($page*20)-20));
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

  		$Justaddedanime = Upcoming::parse($animecontent,'anime');
 		return $Justaddedanime;
	}

	public function getMangaJustaddedAction(Request $request)
	{
		#http://myanimelist.net/anime.php?o=9&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=2&w=1&show=#{page}

		$page = (int) $request->query->get('page');

		if ($page <= 0){
			$page = 1;
		}

		$downloader = $this->get('atarashii_api.communicator');

		try {
			$mangacontent = $downloader->fetch('/manga.php?o=9&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=2&w=1&show='.(($page*20)-20));
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

 		$Justaddedmanga = Upcoming::parse($mangacontent,'manga');

 		return $Justaddedmanga;
	}
}