<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Guzzle\Http\Client;
use Atarashii\APIBundle\Model\Anime;

use \SimpleXMLElement;

class AnimeListController extends FOSRestController
{

    /*
     * Animelist get action
     * @var string $username username
     * @return array
     *
     * @Rest\View()
     */
	public function getAction($username)
	{
		#http://myanimelist.net/malappinfo.php?u=#{username}&status=all&type=anime

		$downloader = $this->get('atarashii_api.downloader');
		$animelistcontent = $downloader->fetch('/malappinfo.php?u=' . $username . '&status=all&type=anime');

		if (strpos($animelistcontent,'Invalid username') !== false){
			$animelist = 'Failed to find the specified user, please try again.';
		}else{
			$animelistxml = new SimpleXMLElement($animelistcontent);
			$alist = array();

			$i = 0;
			foreach ($animelistxml->anime as $anime) {
				$alist[$i] = new Anime();
				$alist[$i]->id = (int) $anime->series_animedb_id;
				$alist[$i]->title = (string) $anime->series_title;
				$alist[$i]->type = Anime::parseAnimeType((int) $anime->series_type);
				$alist[$i]->status = Anime::parseAnimeStatus((int) $anime->series_status);
				$alist[$i]->episodes = (int) $anime->series_episodes;
				$alist[$i]->image_url = (string) $anime->series_image;
				$alist[$i]->listed_anime_id = (int) $anime->my_id;
				$alist[$i]->watched_episodes = (int) $anime->my_watched_episodes;
				$alist[$i]->score = (int) $anime->my_score;
				$alist[$i]->watched_status = Anime::parseWatchedStatus((int) $anime->my_status);
				$i++;
			}

			$animelist['statistics']['days'] = (float) $animelistxml->myinfo->user_days_spent_watching;
			$animelist['anime'] = $alist;
		}
 		return $animelist;
	}
    /*
     * aud actions (add/update/delete anime)
     * $username username
     * $password password
     *
     * anime_id: [animeid] (if you use this with a update it will be ignored)
     * status: [1= watching],[2= completed],[3= onhold],[4= dropped],[5= plantowatch] (Default 1).
     * episodes: [episodes] (default 0)
     * Score: [score] (1/10)
     *
     * note: delete does ignore all the body parameters!
     */
	public function audAction(Request $request, $id)
	{
		#http://myanimelist.net/api/animelist/add/#{id}.xml
		#http://myanimelist.net/api/animelist/update/#{id}.xml
		#http://myanimelist.net/api/animelist/delete/#{id}.xml

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
		$status = $this->parse('status','episodes',$body);
		if ($status == ''){
			$body = $body.'status';
		}
		if ($request->isMethod('post')){
			$id = $this->parse('anime_id','status',$body);
			$type = 'add';
		}elseif ($request->isMethod('put')){
			$type = 'update';
		}elseif ($request->isMethod('delete')){
			$type = 'delete';
		}else{
			return $this->view(Array('error' => 'GET request is not allowed'), 405);
		}

		$episode = $this->parse('episodes','score',$body);
		if (strpos($episode,'anime_id') !== false){
			$episode = '0';
		}
		$score = str_replace('anime_id'.$id,'',$body);
		$score = str_replace('status'.$status,'',$score);
		$score = str_replace('episodes'.$episode,'',$score);
		$score = str_replace('score','',$score);
		$status = Anime::getWatchedStatus($status);

		//Creating request
		$client = new Client('http://myanimelist.net');
		$client->setUserAgent('Atarashii');
		$request = $client->post('/api/animelist/'.$type.'/'.$id.'.xml');
		$request->setAuth($username, $password);

		//setup of the xml
		$requestbody= Anime::setxmlAnime($episode,$status,$score);
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