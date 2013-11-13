<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Atarashii\APIBundle\Model\Anime;

use \SimpleXMLElement;

class AnimeListController extends FOSRestController
{

    /**
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

}