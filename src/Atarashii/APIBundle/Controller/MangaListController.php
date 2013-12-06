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
		$body = str_replace("=", "", $body);
		$body = str_replace("-", "", $body);
		$body = str_replace(" ", "", $body);

		//get the REST type & parse the data
		$status = $this->parse('status','chapters',$body);
		if (empty($status)){
			$body = $body.'status';
		}
		if ($request->isMethod('post')){
			$id = $this->parse('manga_id','status',$body);
			$type = 'add';
		}elseif ($request->isMethod('put')){
			$type = 'update';
		}elseif ($request->isMethod('delete')){
			$type = 'delete';
		}else{
			return $this->view(Array('error' => 'GET request is not allowed'), 405);
		}
		$volumes  = $this->parse('volumes','score',$body);
		if (strpos($volumes,'manga_id') !== false){
			$chapters = $this->parse('chapters','score',$body);
			$volumes = '0';
		}else{
			$chapters = $this->parse('chapters','volumes',$body);
		}
		if (strpos($chapters,'manga_id') !== false){
			$chapters = '0';
		}
		$score = str_replace('manga_id'.$id,'',$body);
		$score = str_replace('status'.$status,'',$score);
		$score = str_replace('chapters'.$chapters,'',$score);
		$score = str_replace('volumes'.$volumes,'',$score);
		$score = str_replace('score','',$score);
		$status = Manga::getReadStatus($status);

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
			return $this->view(Array('error' => 'not-found'), 500);
		}
	}

     /*
     * Parse action
     * $first the text before your string
     * $second the text after your string
     * $string the text which contains the wanted string
     *
     * note: if the $string doesn't has the $second or $first it will return empty!
     */
	public function parse($first,$second,$string)
	{
		$startsAt = strpos($string, $first);
		$endsAt = strpos($string, $second, $startsAt);
		$parse = substr($string, $startsAt, $endsAt - $startsAt);
		$parsed = str_replace($first, '', $parse);
		return($parsed);
	}
}