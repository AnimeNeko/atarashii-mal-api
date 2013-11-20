<?php
namespace Atarashii\APIBundle\Parser;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;
use Atarashii\APIBundle\Model\Anime;

class Popular {

	public function parse($contents,$type) {
		$crawler = new Crawler();
		$crawler->addHTMLContent($contents, 'UTF-8');
		$maincontent = $crawler->filter('#horiznav_nav')->nextAll();

		//decides which type it is.
		if (strpos($type,'anime') !== false){
			return ($this->parserecord($maincontent,'Anime'));
		}else{
			return ($this->parserecord($maincontent,'Manga'));
		}
	}

	private function parserecord($content,$typeserie) {

	$elements = $content->filterXPath('//table[1]')->filter('tr');
	$array = array();

		foreach ($elements as $content) {

			$crawler = new Crawler($content);
			$details = new Anime();
			$id = str_replace('#area','',$crawler->filter('a')->attr('id'));
			$title = trim($crawler->filter('strong')->text());
			//I removed the 't' because it will return a little image
			$image_url = str_replace('t.j','.j',$crawler->filter('img')->attr('src'));
			$members_count = str_replace(' members','',trim($crawler->filterXPath('//td[3]')->filter('span')->text()));

			//Info contains: TV, 37 eps, scored 8.82
			$info = trim($crawler->filterXPath('//td[3]//div[2]')->text());
			//Get the type
			$type = 'Error';
			if (strpos($typeserie,'Anime') !== false){
				if (strpos($info,'TV') !== false){
					$type = 'TV';
				}elseif (strpos($info,'OVA') !== false){
					$type = 'OVA';
				}elseif (strpos($info,'Movie') !== false){
					$type = 'Movie';
				}elseif (strpos($info,'Special') !== false){
					$type = 'Special';
				}elseif (strpos($info,'ONA') !== false){
					$type = 'ONA';
				}elseif (strpos($info,'Music') !== false){
					$type = 'Music';
				}
				//Get the episodes number & score
				$episodes = $this->parsestring($info,$type.', ',' eps,');
				$score = str_replace($members_count.' members','',str_replace($type.', '.$episodes.' eps, scored ','',$info));
			}else{
				$episodes = substr($info, 0, strpos($info, ' volumes'));// $this->parsestring($info,'',' volumes,');
				$score = str_replace($members_count.' members','',str_replace($episodes.' volumes, scored ','',$info));
				$type = 'Unknown';
			}

			//Setting up the AnimeDetails
			$key ='id';$details->$key = $id;
			$key ='title';$details->$key = $title;
			$key ='image_url';$details->$key = $image_url;
			$key ='members_count';$details->$key = $members_count;
			$key ='type';$details->$key = $type;
			$key ='episodes';$details->$key = $episodes;
			$key ='score';$details->$key = $score;

			array_push($array, $details);
		}
		return $array;
	}

	// $string = the string wich contains the text, $first = before the text, $second = after the text
	function parsestring($string,$first,$second){
		$startsAt = strpos($string, $first);
		$endsAt = strpos($string, $second, $startsAt);
		$parse = substr($string, $startsAt, $endsAt - $startsAt);
		$parsed = str_replace($first, '', $parse);
		return($parsed);
	}
}