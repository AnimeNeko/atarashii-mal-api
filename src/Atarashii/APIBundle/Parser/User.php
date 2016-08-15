<?php
/**
* Atarashii MAL API.
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014-2015 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/
namespace Atarashii\APIBundle\Parser;

use Atarashii\APIBundle\Model\ProfileDetails;
use Symfony\Component\DomCrawler\Crawler;
use Atarashii\APIBundle\Model\Profile;
use Atarashii\APIBundle\Model\Anime;
use Atarashii\APIBundle\Model\Manga;
use Atarashii\APIBundle\Helper\Date;

class User
{
    public static function parse($contents, $apiVersion)
    {
        $user = new Profile();

        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');

        $profileContent = $crawler->filter('#contentWrapper');

        $avatar = $profileContent->filter('.user-image img');

        if ($avatar->count() > 0) {
            $user->setAvatarUrl($avatar->attr('src'));
        }

        $animeStats = $profileContent->filter('.stats')->filter('.anime');
        $mangaStats = $profileContent->filter('.stats')->filter('.manga');

        $user->details = self::parseDetails($profileContent, $user->details, $apiVersion); //Details is an object, so we need to pass it to the function.
        $user->anime_stats = self::parseStats($animeStats, $user->anime_stats, 'anime', $apiVersion);
        $user->manga_stats = self::parseStats($mangaStats, $user->manga_stats, 'manga', $apiVersion);

        return $user;
    }

    private static function parseDetails(Crawler $content, ProfileDetails $details, $apiVersion)
    {
        $userAccessLevel = $content->filterXPath('//*[contains(attribute::class,"profile-team-title")]');

        $userStats = $content->filter('.user-status');

        $userOnline = $userStats->filterXPath('//*[text()="Last Online"]/../span[2]');
        $userGender = $userStats->filterXPath('//*[text()="Gender"]/../span[2]');
        $userBDay = $userStats->filterXPath('//*[text()="Birthday"]/../span[2]');
        $userLoc = $userStats->filterXPath('//*[text()="Location"]/../span[2]');
        $userJoined = $userStats->filterXPath('//*[text()="Joined"]/../span[2]');
        $userForumPosts = $userStats->filterXPath('//*[text()="Forum Posts"]/../span[2]');
        $userWebsite = $content->filterXPath('//*[@class="user-profile-sns"][1]/a');
        $comments = $content->filter('span[class="floatRightHeader ff-Verdana"]');

        if ($comments->count() > 0) {
            $details->setComments((int) trim(preg_replace('/(.+?)\((.+?)\)/', '$2', $comments->text())));
        }
        
        if ($userAccessLevel->count() > 0) {
            $details->setAccessRank($userAccessLevel->text());
        } else {
            $details->setAccessRank('Member');
        }

        if ($userOnline->count() > 0) {
            $details->setLastOnline($userOnline->text());
        }

        if ($userGender->count() > 0) {
            $details->setGender($userGender->text());
        } else {
            $details->setGender('Not specified');
        }

        if ($userBDay->count() > 0) {
            //MAL allows partial birthdays, even just day and year (wth?)
            //We need to check combinations to handle this correctly.
            $bdParts = explode(' ', $userBDay->text());

            if (count($bdParts) == 3) { //Full date, normal processing
                $details->setBirthday(\DateTime::createFromFormat('M d, Y', $userBDay->text())->format('F j, Y'));
            } elseif (count($bdParts) == 2) { //We only have two parts, figure out what we were given
                $firstIsNumber = is_numeric($bdParts[0]);
                $hasComma = strpos($bdParts[0], ',');

                if (($firstIsNumber === false) && ($hasComma === false)) {
                    //First Value must be a month
                    //This will cover month and day or month and year
                    $monthName = \DateTime::createFromFormat('M d', $bdParts[0].' 1')->format('F');
                    $details->setBirthday($monthName.' '.$bdParts[1]);
                } else { //Day and year make no sense, just use year
                    $details->setBirthday($bdParts[1]);
                }
            } else { //It's either just one value or something else, just save the data.
                $details->setBirthday($userBDay->text());
            }
        }

        if ($userJoined->count() > 0) {
            $details->setJoinDate(\DateTime::createFromFormat('M d, Y', $userJoined->text())->format('F j, Y'));
        }

        if ($userLoc->count() > 0) {
            $details->setLocation($userLoc->text());
        }

        if ($userWebsite->count() > 0) {
            $details->setWebsite($userWebsite->attr('href'));
        }

        if ($userForumPosts->count() > 0) {
            $details->setForumPosts((int) str_replace(',', '', $userForumPosts->text()));
        }

        if ($apiVersion >= '2.1') {
            $userReviews = $userStats->filterXPath('//*[text()="Reviews"]/../span[2]');
            $userRecommendations = $userStats->filterXPath('//*[text()="Recommendations"]/../span[2]');
            $userBlogPosts = $userStats->filterXPath('//*[text()="Blog Posts"]/../span[2]');
            $userClub = $userStats->filterXPath('//*[text()="Clubs"]/../span[2]');

            if ($userReviews->count() > 0) {
                $details->setReviews((int) $userReviews->text());
            }

            if ($userRecommendations->count() > 0) {
                $details->setRecommedations((int) $userRecommendations->text());
            }

            if ($userBlogPosts->count() > 0) {
                $details->setBlogPosts((int) $userBlogPosts->text());
            }

            if ($userClub->count() > 0) {
                $details->setClubs((int) $userClub->text());
            }
        }

        return $details;
    }

    private static function parseStats(Crawler $content, $stats, $mediaType, $apiVersion)
    {

        //General header stuff
        $genStats = $content->filterXPath('//*[contains(attribute::class,"stat-score")]');

        $timeDays = $genStats->filterXPath('//*[normalize-space(.)="Days:"]/..');

        if ($timeDays->count() > 0) {
            $days = explode(' ', $timeDays->text());
            $stats->setTimeDays((float) $days[1]);
        }

        //Individual Status Items
        $statsStatus = $content->filterXPath('//ul[contains(attribute::class,"stats-status")]');

        //Watching/Reading and Planned are different for anime/manga
        if ($mediaType == 'anime') {
            $inProgress = $statsStatus->filterXPath('//*[contains(attribute::class,"watching")]/../span');
            $planned = $statsStatus->filterXPath('//*[contains(attribute::class,"plantowatch")]/../span');

            if ($inProgress->count() > 0) {
                $stats->setWatching((int) str_replace(',', '', $inProgress->text()));
            }

            if ($planned->count() > 0) {
                $stats->setPlanToWatch((int) str_replace(',', '', $planned->text()));
            }
        } elseif ($mediaType == 'manga') {
            $inProgress = $statsStatus->filterXPath('//*[contains(attribute::class,"reading")]/../span');
            $planned = $statsStatus->filterXPath('//*[contains(attribute::class,"plantoread")]/../span');

            if ($inProgress->count() > 0) {
                $stats->setReading((int) str_replace(',', '', $inProgress->text()));
            }

            if ($planned->count() > 0) {
                $stats->setPlanToRead((int) str_replace(',', '', $planned->text()));
            }
        }

        $completed = $statsStatus->filterXPath('//*[contains(attribute::class,"completed")]/../span');
        $onHold = $statsStatus->filterXPath('//*[contains(attribute::class,"on-hold")]/../span');
        $dropped = $statsStatus->filterXPath('//*[contains(attribute::class,"dropped")]/../span');

        if ($completed->count() > 0) {
            $stats->setCompleted((int) str_replace(',', '', $completed->text()));
        }

        if ($onHold->count() > 0) {
            $stats->setOnHold((int) str_replace(',', '', $onHold->text()));
        }

        if ($dropped->count() > 0) {
            $stats->setDropped((int) str_replace(',', '', $dropped->text()));
        }

        //Summary Stats
        $statsSummary = $content->filterXPath('//ul[contains(attribute::class,"stats-data")]');

        $totalEntries = $statsSummary->filterXPath('//li[1]/span[2]');

        if ($totalEntries->count() > 0) {
            $stats->setTotalEntries((int) str_replace(',', '', $totalEntries->text()));
        }

        if ($apiVersion >= '2.1') {
            $meanScore = $genStats->filterXPath('//*[normalize-space(.)="Mean Score:"]/..');
            if ($meanScore->count() > 0) {
                $score = explode(' ', $meanScore->text());
                $stats->setMeanScore((float) $score[2]);
            }

            $reRecord = $statsSummary->filterXPath('//li[2]/span[2]');
            $countRecord = $statsSummary->filterXPath('//li[3]/span[2]');
            if ($mediaType == 'anime') {
                if ($reRecord->count() > 0) {
                    $stats->setRewatched((int) $reRecord->text());
                }

                if ($countRecord->count() > 0) {
                    $stats->setEpisodes((int) str_replace(',', '', $countRecord->text()));
                }
            } else {
                if ($reRecord->count() > 0) {
                    $stats->setReread((int) $reRecord->text());
                }

                if ($countRecord->count() > 0) {
                    $stats->setVolumes((int) str_replace(',', '', $countRecord->text()));
                }
            }
        }

        return $stats;
    }

    public static function parseFriends($contents)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');
        $maincontent = $crawler->filter('.friendHolder');

        //Empty array so we return something non-null if the list is empty.
        $friendlist = array();

        foreach ($maincontent as $friendentry) {
            $crawler = new Crawler($friendentry);

            //All the data extraction.
            $avatar = $crawler->filter('.friendIcon')->filterXPath('./div/a/img');
            $name = $crawler->filterXPath('//div[@class="friendBlock"]/div[2]/a')->text();
            $lastonline = $crawler->filterXPath('./div/div/div[3]')->text();
            $friendssince = Date::formatTime(str_replace('Friends since ', '', $crawler->filterXPath('./div/div/div[4]')->text()));

            //Remove the tumbnail portions from the URL to get the full image.
            $avatar = str_replace('thumbs/', '', str_replace('_thumb', '', $avatar->attr('src')));

            $friendinfo['name'] = $name;
            $friendinfo['friend_since'] = $friendssince;

            //Fill out a profile object for this user with what information we can extract
            $friendinfo['profile'] = new Profile();
            $friendinfo['profile']->setAvatarUrl($avatar);
            $friendinfo['profile']->details->setLastOnline($lastonline);

            //to force json array and !objects.
            $friendinfo['profile']->manga_stats = null;
            $friendinfo['profile']->anime_stats = null;

            $friendlist[] = $friendinfo;
        }

        return $friendlist;
    }

    public static function parseHistory($contents)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($contents, 'UTF-8');
        $maincontent = $crawler->filter('table')->filter('tr');

        //Empty array so we return something non-null if the list is empty.
        $historylist = array();

        foreach ($maincontent as $historyentry) {
            $crawler = new Crawler($historyentry);

            // bypass for the MAL generated strings
            if (($crawler->filter('a')->count()) > 0) {
                if (strpos($crawler->filter('a')->attr('href'), 'anime') !== false) {
                    $historyinfo['item'] = new Anime();
                    $historyinfo['item']->setWatchedEpisodes((int) $crawler->filter('strong')->text());
                    $historyinfo['type'] = 'anime';
                } else {
                    $historyinfo['item'] = new Manga();
                    $historyinfo['item']->setChaptersRead((int) $crawler->filter('strong')->text());
                    $historyinfo['type'] = 'manga';
                }

                $historyinfo['item']->setTitle($crawler->filter('a')->text());
                $historyinfo['item']->setId((int) str_replace('/'.$historyinfo['type'].'.php?id=', '', $crawler->filter('a')->attr('href')));
                $historyinfo['time_updated'] = Date::formatTime(substr($crawler->filter('td')->eq(1)->text(), 1));

                $historylist[] = $historyinfo;
            }
        }

        return $historylist;
    }
}
