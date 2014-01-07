<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Guzzle\Http\Client;
use Atarashii\APIBundle\Model\Manga;

use \SimpleXMLElement;

class MangaListController extends FOSRestController {
    /**
     * Mangalist get action
     * @var string $username username
     * @return array
     *
     * @Rest\View()
     */
	public function getAction($username) {
		#http://myanimelist.net/malappinfo.php?u=#{username}&status=all&type=manga

		$downloader = $this->get('atarashii_api.communicator');

		try {
			$mangalistcontent = $downloader->fetch('/malappinfo.php?u=' . $username . '&status=all&type=manga');
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

		if (strpos($mangalistcontent,'Invalid username') !== false){
			$mangalist = 'Failed to find the specified user, please try again.';
		}else{
			$mangalistxml = new SimpleXMLElement($mangalistcontent);
			$mlist = array();

			$i = 0;
			foreach ($mangalistxml->manga as $manga) {
				$mlist[$i] = new Manga();
				$mlist[$i]->id = (int) $manga->series_mangadb_id;
				$mlist[$i]->title = (string) $manga->series_title;
				$mlist[$i]->setType((int) $manga->series_type);
				$mlist[$i]->setStatus((int) $manga->series_status);
				$mlist[$i]->chapters = (int) $manga->series_chapters;
				$mlist[$i]->volumes = (int) $manga->series_volumes;
				$mlist[$i]->image_url = (string) $manga->series_image;
				$mlist[$i]->listed_manga_id = (int) $manga->my_id;
				$mlist[$i]->volumes_read = (int) $manga->my_read_volumes;
				$mlist[$i]->chapters_read = (int) $manga->my_read_chapters;
				$mlist[$i]->score = (int) $manga->my_score;
				$mlist[$i]->setReadStatus($manga->my_status);
				$i++;
			}

			$mangalist['statistics']['days'] = (float) $mangalistxml->myinfo->user_days_spent_watching;
			$mangalist['manga'] = $mlist;
		}
 		return $mangalist;
	}

	public function addAction(Request $request) {
		#http://mymangalist.net/api/mangalist/add/#{id}.xml

		//get the credentials we received
		$username = $this->getRequest()->server->get('PHP_AUTH_USER');
		$password = $this->getRequest()->server->get('PHP_AUTH_PW');

		//Don't bother making a request if the user didn't send any authentication
		if($username == null) {
			$view = $this->view(Array('error' => 'unauthorized'), 401);
			$view->setHeader('WWW-Authenticate', 'Basic realm="mymangalist.net"');
			return $view;
		}

		$manga = new Manga();
		$manga->id = $request->request->get('manga_id');
		$manga->read_status = $request->request->get('status');
		$manga->chapters_read = $request->request->get('chapters');
		$manga->volumes_read = $request->request->get('volumes');
		$manga->score = $request->request->get('score');

		$xmlcontent = $manga->MALApiXml();

		$connection = $this->get('atarashii_api.communicator');

		try {
			$result = $connection->sendXML('/api/mangalist/add/' . $manga->id . '.xml', $xmlcontent, $username, $password);
			return $this->view('ok', 201);
		} catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
			return $this->view(Array('error' => 'unauthorized'), 401);
		} catch (\Guzzle\Http\Exception\ServerErrorResponseException $e) {
			return $this->view(Array('error' => 'not-found'), 404);
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

	}

	public function updateAction(Request $request, $id) {
		#http://mymangalist.net/api/mangalist/update/#{id}.xml

		//get the credentials we received
		$username = $this->getRequest()->server->get('PHP_AUTH_USER');
		$password = $this->getRequest()->server->get('PHP_AUTH_PW');

		//Don't bother making a request if the user didn't send any authentication
		if($username == null) {
			$view = $this->view(Array('error' => 'unauthorized'), 401);
			$view->setHeader('WWW-Authenticate', 'Basic realm="mymangalist.net"');
			return $view;
		}

		$manga = new Manga();
		$manga->id = $id;
		$manga->read_status = $request->request->get('status');
		$manga->chapters_read = $request->request->get('chapters');
		$manga->volumes_read = $request->request->get('volumes');
		$manga->score = $request->request->get('score');

		$xmlcontent = $manga->MALApiXml();

		$connection = $this->get('atarashii_api.communicator');

		try {
			$result = $connection->sendXML('/api/mangalist/update/' . $manga->id . '.xml', $xmlcontent, $username, $password);
			return $this->view('ok', 200);
		} catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
			return $this->view(Array('error' => 'unauthorized'), 401);
		} catch (\Guzzle\Http\Exception\ServerErrorResponseException $e) {
			return $this->view(Array('error' => 'not-found'), 404);
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}

	}

	public function deleteAction(Request $request, $id) {
		#http://mymangalist.net/api/mangalist/delete/#{id}.xml

		//get the credentials we received
		$username = $this->getRequest()->server->get('PHP_AUTH_USER');
		$password = $this->getRequest()->server->get('PHP_AUTH_PW');

		//Don't bother making a request if the user didn't send any authentication
		if($username == null) {
			$view = $this->view(Array('error' => 'unauthorized'), 401);
			$view->setHeader('WWW-Authenticate', 'Basic realm="mymangalist.net"');
			return $view;
		}

		$connection = $this->get('atarashii_api.communicator');

		try {
			$result = $connection->sendXML('/api/mangalist/delete/' . $id . '.xml', '', $username, $password);
			return $this->view('ok', 200);
		} catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
			return $this->view(Array('error' => 'unauthorized'), 401);
		} catch (\Guzzle\Http\Exception\ServerErrorResponseException $e) {
			return $this->view(Array('error' => 'not-found'), 404);
		} catch (\Guzzle\Http\Exception\CurlException $e) {
			return $this->view(Array('error' => 'network-error'), 500);
		}
	}
}