<?php
namespace Atarashii\APIBundle\Model;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

class Justadded {

	public function parse($contents,$type) {
		$crawler = new Crawler();
		$crawler->addHTMLContent($contents, 'UTF-8');
		$maincontent = $crawler->filter('.borderClass')->first()->nextAll();
		
		//decides which type it is.
		if (strpos($type,'anime') !== false){
			return ($this->parserecord($maincontent,'Anime'));
		}else{
			return ($this->parserecord($maincontent,'Manga'));
		}
	}

	private function parserecord($content,$typeserie) {
	$searchbar = true;
	$check = true;
	$elements = $content->filter('tr');
	
	$array = array();
	
		foreach ($elements as $content) {
			//tricky method to skip the search bar which also is a <tr></tr>
			if ($check == true){
				$check = false;
			}else{
				$crawler = new Crawler($content);
				if (strpos($typeserie,'Anime') !== false){
					$details = new AnimeSearch();
				
					//Custom parsing
					$synopsis = trim($crawler->filterXPath('//td[2]//div[3]')->text());
					$episodes = trim($crawler->filterXPath('//td[4]')->text());
					$members_score = trim($crawler->filterXPath('//td[5]')->text());
					$start_date = trim($crawler->filterXPath('//td[6]')->text());
					$end_date = trim($crawler->filterXPath('//td[7]')->text());
					$classification = trim($crawler->filterXPath('//td[9]')->text());
					$synopsis = trim($crawler->filterXPath('//td[2]//div[3]')->text());
					
					//Setting up the AnimeDetails
					$key ='episodes';$details->$key = $episodes;
					$key ='classification';$details->$key = $classification;
				}else{
					$details = new MangaSearch();
				
					//Custom parsing
					$type = trim($crawler->filterXPath('//td[3]')->text());		
					$volumes = trim($crawler->filterXPath('//td[4]')->text());
					$chapters = trim($crawler->filterXPath('//td[5]')->text());
					$members_score = trim($crawler->filterXPath('//td[6]')->text());
					$start_date = trim($crawler->filterXPath('//td[7]')->text());
					$end_date = trim($crawler->filterXPath('//td[8]')->text());
					$synopsis = trim($crawler->filterXPath('//td[2]//div[2]')->text());	
					
					//Setting up the MangaDetails
					$key ='volumes';$details->$key = $volumes;
					$key ='chapters';$details->$key = $chapters;
				}
				//universal parsing 
				$id = str_replace('#sarea','',$crawler->filter('a')->attr('id'));
				$title = $crawler->filter('strong')->text();
				//I removed the 't' because it will return a little image
				$image_url = str_replace('t.j','.j',$crawler->filter('img')->attr('src'));
				$type = trim($crawler->filterXPath('//td[3]')->text());	
				
				//universal details 
				$key ='id';$details->$key = $id;
				$key ='title';$details->$key = $title;
				$key ='type';$details->$key = $type;
				$key ='synopsis';$details->$key = $synopsis;
				$key ='image_url';$details->$key = $image_url;
				$key ='members_score';$details->$key = $members_score;
				$key ='start_date';$details->$key = $start_date;
				$key ='end_date';$details->$key = $end_date;
				
				array_push($array, $details);
			}
		}
		return $array;
	}
}

Class AnimeSearch {
	public $id;
	public $title;
	public $episodes;
	public $type;
	public $synopsis;
	public $image_url;
	public $members_score;
	public $start_date;
	public $end_date;
	public $classification;
}

Class MangaSearch {
	public $id;
	public $title;
	public $volumes;
	public $chapters;
	public $type;
	public $synopsis;
	public $image_url;
	public $members_score;
	public $start_date;
	public $end_date;
}