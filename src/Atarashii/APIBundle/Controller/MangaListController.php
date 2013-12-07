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

		$downloader = $this->get('atarashii_api.downloader');
		$mangalistcontent = $downloader->fetch('/malappinfo.php?u=' . $username . '&status=all&type=manga');

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
				$mlist[$i]->type = Manga::parseMangaType((int) $manga->series_type);
				$mlist[$i]->status = Manga::parseMangaStatus((int) $manga->series_status);
				$mlist[$i]->chapters = (int) $manga->series_chapters;
				$mlist[$i]->volumes = (int) $manga->series_volumes;
				$mlist[$i]->image_url = (string) $manga->series_image;
				$mlist[$i]->listed_manga_id = (int) $manga->my_id;
				$mlist[$i]->volumes_read = (int) $manga->my_read_volumes;
				$mlist[$i]->chapters_read = (int) $manga->my_read_chapters;
				$mlist[$i]->score = (int) $manga->my_score;
				$mlist[$i]->read_status = Manga::parseReadStatus((int) $manga->my_status);
				$i++;
			}

			$mangalist['statistics']['days'] = (float) $mangalistxml->myinfo->user_days_spent_watching;
			$mangalist['manga'] = $mlist;
		}
 		return $mangalist;
	}
    /*
     * aud actions (add/update/delete manga)
     * $username username
     * $password password
     *
     * manga_id: [mangaid] (if you use this with a update it will be ignored)
     * status: [1= reading],[2= completed],[3= onhold],[4= dropped],[6= plantoread] (Default 1).
     * chapters: [chapters] default 0)
     * volumes: [volumes] (default 0)
     * Score: [score] (1/10)
     *
     * note: delete does ignore all the body parameters!
     */
	public function audAction(Request $request, $id)
	{
		#http://myanimelist.net/api/mangalist/add/#{id}.xml
		#http://myanimelist.net/api/mangalist/update/#{id}.xml
		#http://myanimelist.net/api/mangalist/delete/#{id}.xml

		//Get the body send by atarashii
		$username = $this->getRequest()->server->get('PHP_AUTH_USER');
		$password = $this->getRequest()->server->get('PHP_AUTH_PW');
		if($username == null) {
			return $this->view(Array('error' => 'unauthorized'), 401);
		}

		//Remove some stuff we don't need (trim doesn't help always)
		$body = trim($request->getContent());
		$body = str_replace("\n", "", $body);
		$body = str_replace("-", "", $body);
		$body = str_replace(" ", "", $body);
		$body = str_replace("&", "", $body);
		$bodyarray = $this->parse($body);

		//get the REST type & parse the data
		if ($request->isMethod('post')){
			$id = $bodyarray[1];
			$type = 'add';
		}elseif ($request->isMethod('put')){
			$type = 'update';
		}elseif ($request->isMethod('delete')){
			$type = 'delete';
		}else{
			return $this->view(Array('error' => 'GET request is not allowed'), 405);
		}
		$status = $bodyarray[2];
		$chapters = $bodyarray[3];
		$volumes = $bodyarray[4];
		$score = $bodyarray[5];

		//Creating request
		$client = new Client('http://myanimelist.net');
		$client->setUserAgent('Atarashii');
		$request = $client->post('/api/mangalist/'.$type.'/'.$id.'.xml');
		$request->setAuth($username, $password);

		//setup of the xml
		$requestbody= Manga::setxmlManga($chapters,$volumes,$status,$score);
		$request->setPostField('data',$requestbody);

		// Verify and send the request.
		try {
			$response = $request->send();
			return $this->view(Array('authorized' => 'OK'), 200);
		}
		catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
			return $this->view(Array('error' => 'unauthorized'), 401);
		}
		catch (\Guzzle\Http\Exception\ServerErrorResponseException $e) {
			$details = Array('id' => $id, 'status' => $status, 'chapters' => $chapters, 'volumes' => $volumes, 'score' => $score, 'body' => $body, 'command' => $type);
			return $this->view(Array('error' => 'not-found','received details' => $details), 500);
		}
	}

     /*
     * Parse action
     * $string body
     * Returns an array.
     *
     * note: if the $string doesn't has the parameter it will return empty!
     */
	public function parse($string)
	{
		//Default values
		$manga_id = 0;
		$status = '1';
		$chapters = 0;
		$volumes = 0;
		$score = 0;

		//tricky methode to get the last string
		$string = $string.'=';

		//parsing
		for( $i = 0; $i < 5; $i += 1) {
			$first = current(explode("=", $string));
			$second = current(explode("=",str_replace($first.'=','',$string)));

			if (strpos($second,'manga_id') !== false){
				$second = str_replace('manga_id','',$second);
			}elseif (strpos($second,'status') !== false){
				$second = str_replace('status','',$second);
			}elseif (strpos($second,'chapters') !== false){
				$second = str_replace('chapters','',$second);
			}elseif (strpos($second,'volumes') !== false){
				$second = str_replace('volumes','',$second);
			}elseif (strpos($second,'score') !== false){
				$second = str_replace('score','',$second);
			}

			if (strpos($first,'manga_id') !== false){
				$manga_id = $second;
			}elseif (strpos($first,'status') !== false){
				$status = Manga::getReadStatus($second);
			}elseif (strpos($first,'chapters') !== false){
				$chapters = $second;
			}elseif (strpos($first,'volumes') !== false){
				$volumes = $second;
			}elseif (strpos($first,'score') !== false){
				$score = $second;
			}
			$string = str_replace($first.'='.$second,'',$string);
		}
		return array(1 => $manga_id, 2 => $status, 3 => $chapters, 4 => $volumes, 5 => $score);
	}
}