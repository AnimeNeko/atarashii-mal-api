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
     * Animelist post action (add anime)
     * $username username
     * $password password
     *
     * anime_id: [animeid]
     * status: [1= watching],[2= completed],[3= onhold],[4= dropped],[5= plantowatch] (Default 1).
     * episodes: [episodes]
     * Score: [score] (1/10)
     */
	public function postAction(Request $request)
	{
		#http://myanimelist.net/api/animelist/add/#{id}.xml

		//Get the body send by atarashii
		$username = $this->getRequest()->server->get('PHP_AUTH_USER');
		$password = $this->getRequest()->server->get('PHP_AUTH_PW');
		if($username == null) {
			return $this->view(Array('error' => 'unauthorized'), 401);
		}

		//Remove some stuff we don't need (trim doesn't help always)
		$body = trim($request->getContent());
		$body = str_replace("\n  ", "", $body);
		$body = str_replace("=", "", $body);
		$body = str_replace("-", "", $body);
		$body = str_replace(" ", "", $body);

		//Parse the data
		$status = $this->parse('status','episodes',$body);
		if ($status == ''){
			$body = $body.'status';
		}
		$id = $this->parse('anime_id','status',$body);
		$episode = $this->parse('episodes','score',$body);
		$score = str_replace('anime_id'.$id.'status'.$status.'episodes'.$episode.'score','',$body);
		$status = Anime::getWatchedStatus($status);

		//Creating request
		$client = new Client('http://myanimelist.net');
		$client->setUserAgent('Atarashii');
		$request = $client->post('/api/animelist/add/'.$id.'.xml');
		$request->setAuth($username, $password);

		//setup of the xml
		$requestbody = '<?xml version="1.0" encoding="UTF-8"?><entry>';
		$requestbody = $requestbody.'<episode>'.$episode.'</episode>';
		$requestbody = $requestbody.'<status>'.$status.'</status>';
		$requestbody = $requestbody.'<score>'.$score.'</score>';
		$requestbody = $requestbody.'<downloaded_episodes></downloaded_episodes>';
		$requestbody = $requestbody.'<storage_type></storage_type>';
		$requestbody = $requestbody.'<storage_value></storage_value>';
		$requestbody = $requestbody.'<times_rewatched></times_rewatched>';
		$requestbody = $requestbody.'<rewatch_value></rewatch_value>';
		$requestbody = $requestbody.'<date_start></date_start>';
		$requestbody = $requestbody.'<date_finish></date_finish>';
		$requestbody = $requestbody.'<priority></priority>';
		$requestbody = $requestbody.'<enable_discussion></enable_discussion>';
		$requestbody = $requestbody.'<enable_rewatching></enable_rewatching>';
		$requestbody = $requestbody.'<comments></comments>';
		$requestbody = $requestbody.'<fansub_group></fansub_group>';
		$requestbody = $requestbody.'<tags></tags></entry>';
		$request->setPostField('data',$requestbody);

		// Verify and send the request.
		try {
			$response = $request->send();
			echo $response->getBody();
			return $this->view(Array('authorized' => 'OK'), 200);
		}
		catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
			return $this->view(Array('error' => 'unauthorized'), 401);
		}
		catch (\Guzzle\Http\Exception\ServerErrorResponseException $e) {
			return $this->view(Array('error' => 'not-found'), 500);
		}
	}

	public function parse($first,$second,$string)
	{
		$startsAt = strpos($string, $first);
		$endsAt = strpos($string, $second, $startsAt);
		$parse = substr($string, $startsAt, $endsAt - $startsAt);
		$parsed = str_replace($first, '', $parse);
		return($parsed);
	}
}