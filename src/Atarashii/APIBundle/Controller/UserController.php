<?php
namespace Atarashii\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Atarashii\APIBundle\Model\User;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

use \SimpleXMLElement;
use \DateTime;

class UserController extends FOSRestController
{

    /**
     * Profile get action
     * @var string $username username
     * @return array
     *
     * @Rest\View()
     */
	public function getProfileAction($username)
	{
		#http://myanimelist.net/profile/#{username}

		$downloader = $this->get('atarashii_api.downloader');
		$profilecontent = $downloader->fetch('/profile/' . $username);

		if (strpos($profilecontent,'Failed to find') !== false){
			$userprofile = 'Failed to find the specified user, please try again.';
		}else{
			$userprofile = new User();
			$userprofile->parse($profilecontent);
		}
 		return $userprofile;
	}

	public function getFriendsAction($username)
	{
		#http://myanimelist.net/profile/#{username}/friends

		$downloader = $this->get('atarashii_api.downloader');
		$friendscontent = $downloader->fetch('/profile/' . $username . '/friends');

		if (strpos($friendscontent,'Failed to find') !== false){
			$friendlist = 'Failed to find the specified user, please try again.';
		}else{
			$friendlist = $this->parseFriends($friendscontent);
		}
 		return $friendlist;
	}

	public function parseFriends($contents) {
		$crawler = new Crawler();
		$crawler->addHTMLContent($contents, 'UTF-8');
		$maincontent = $crawler->filter('.friendHolder');

		//Empty array so we return something non-null if the list is empty.
		$friendlist = array();

		foreach($maincontent as $friendentry) {
			$crawler = new Crawler($friendentry);

			//All the data extraction.
			$friendavatar = $crawler->filter('.friendIcon')->filterXPath('./div/a/img');
			$friendname = $crawler->filterXPath('//div[@class="friendBlock"]/div[2]/a')->text();
			$lastonline = $crawler->filterXPath('./div/div/div[3]')->text();
			$friendssince = str_replace('Friends since ', '', $crawler->filterXPath('./div/div/div[4]')->text());

			//Remove the tumbnail portions from the URL to get the full image.
			$friendavatar = str_replace('thumbs/', '', str_replace('_thumb', '', $friendavatar->attr('src')));

			//Sometimes this value doesn't exist, so it should be set as null. Otherwise, format the time to RFC3389.
			if($friendssince != '') {
				$friendssince = DateTime::createFromFormat('m-d-y, g:i A', $friendssince)->format(DateTime::RFC3339);
			}
			else {
				$friendssince = null;
			}

			$friendlist[$friendname]['avatar_url'] = $friendavatar;
			$friendlist[$friendname]['last_online'] = $lastonline;
			$friendlist[$friendname]['friend_since'] = $friendssince;
		}
		return $friendlist;

	}

}