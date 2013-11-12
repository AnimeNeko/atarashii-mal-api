<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
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
}