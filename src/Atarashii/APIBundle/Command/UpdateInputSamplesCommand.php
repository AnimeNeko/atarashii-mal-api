<?php
/**
* Atarashii MAL API.
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014-2017 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/

namespace Atarashii\APIBundle\Command;

use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class UpdateInputSamplesCommand extends ContainerAwareCommand
{
    private static $anonPages = [
        ['Anime 47 (Akira)', '/anime/47', '/anime-47.html'],
        ['Anime 10236 (Kagee Grimm Douwa)', '/anime/10236', '/anime-10236.html'],
        ['Anime 10758 (Momotarou)', '/anime/10758', '/anime-10758.html'],
        ['Anime 18617 (Girls und Panzer Movie)', '/anime/18617', '/anime-18617.html'],
        ['Manga 137 (R.O.D: Read or Die)', '/manga/137', '/manga-137.html'],
        ['Manga 44347 (One-Punch Man)', '/manga/44347', '/manga-44347.html'],
        ['Reviews for Anime 1887 (Lucky Star)', '/anime/1887/_/reviews', '/anime-1887-reviews.html'],
        ['Reviews for Manga 11977 (Bambino!)', '/manga/11977/_/reviews', '/manga-11977-reviews.html'],
        ['Recommendations for Anime 21 (One Piece)', '/anime/21/_/userrecs', '/anime-21-recs.html'],
        ['Recommendations for Manga 21 (Death Note)', '/manga/21/_/userrecs', '/manga-21-recs.html'],
        ['Characters for Anime 1887 (Lucky Star)', '/anime/1887/_/characters', '/anime-1887-cast.html'],
        ['Episodes for Anime 21 (One Piece)', '/anime/21/_/episode', '/anime-21-eps.html'],
        ['Top Anime List', '/topanime.php?limit=0', '/anime-top.html'],
        ['Top Manga List', '/topmanga.php?limit=0', '/manga-top.html'],
        ['Upcoming Anime List for January 1930', '/anime.php?sd=01&sm=01&sy=1930&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show=0', '/anime-upcoming-1930.html'],
        ['Upcoming Anime List for June 2016', '/anime.php?sd=09&sm=06&sy=2016&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show=0', '/anime-upcoming.html'],
        ['Upcoming Manga List for June 2016', '/manga.php?sd=09&sm=06&sy=2016&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show=0', '/manga-upcoming.html'],
        ['Anime Season Schedule', '/anime/season/schedule', '/schedule-26052016.html'],
        ['Forum Index', '/forum/', '/forum-index.html'],
        ['Forum Board 14 (MAL Guidelines & FAQ)', '/forum/?board=14', '/forum-board-14.html'],
        ['Forum Sub-Board 2 (Anime DB)', '/forum/?subboard=2', '/forum-sub-2.html'],
        ['Forum Topic 516059 (Site & Forum Guidelines)', '/forum/?topicid=516059', '/forum-topic-516059.html'],
        ['Person 10 (Bosch, Johnny Yong)', '/people/10', '/people-10.html'],
        ['Person 159 (Rial, Monica)', '/people/159', '/people-159.html'],
        ['Person 185 (Hanazawa, Kana)', '/people/185', '/people-185.html'],
        ['Person 1870 (Miyazaki, Hayao)', '/people/1870', '/people-1870.html'],
        ['Person 2608 (Miyamoto, Kano)', '/people/2608', '/people-2608.html'],
        ['Person 7277 (Ikimono-gakari)', '/people/7277', '/people-7277.html'],
        ['Person 10145 (huke)', '/people/10145', '/people-10145.html'],
        ['Person 11746 (ClariS)', '/people/11746', '/people-11746.html'],
        ['Profile for User Xinil', '/profile/xinil', '/profile-xinil.html'],
        ['Profile for User Motoko', '/profile/motoko', '/profile-motokochan.html'],
        ['Friendlist for User Motoko', '/profile/motoko/friends', '/profile-motokochan-friends.html'],
        ['History for User Motoko', '/history/motoko', '/history-motokochan.html'],
    ];

    //NOTE: PHP doesn't allow expressions in default values, so we use placeholders.
    //Use __MESSAGEID__ for the user message ID in the URL.
    //We will replace these in the function.
    private static $authPages = [
        ['Anime 1887 Personal (Lucky Star)', '/anime/1887', '/anime-1887-mine.html'],
        ['Manga 11977 Personal (Bambino!)', '/manga/11977', '/manga-11977-mine.html'],
        ['Anime 1689 Personal Details (5cm/s)', '/ownlist/anime/1689/edit?hideLayout', '/anime-1689-mine-detailed.html'],
        ['Manga 11977 Personal Details (Bambino!)', '/ownlist/manga/11977/edit?hideLayout', '/manga-11977-mine-detailed.html'],
        ['Manga 17074 Personal Details (We Can Fly!)', '/ownlist/manga/17074/edit?hideLayout', '/manga-17074-mine-detailed.html'],
        ['Anime 1689 History (5cm/s)', '/ajaxtb.php?detailedaid=1689', '/anime-1689-history.html'],
        ['Manga 11977 History (Bambino!)', '/ajaxtb.php?detailedmid=11977', '/manga-11977-history.html'],
        ['Account Profile Details', '/editprofile.php', '/editprofile.html'],
        ['User Messages List', '/mymessages.php', '/user-messages-list.html'],
        ['User Message', '/mymessages.php?go=read&id=__MESSAGEID__', '/user-message.html'],
    ];

    protected function configure()
    {
        $this
            //name of the command
            ->setName('api:test-samples:update')
            //Short Description
            ->setDescription('Updates the samples used for parser unit tests')
            //Full Help
            ->setHelp('This command will update the sample HTML pages used for the parser unit tests. It uses the login configured in the parameters file for this purpose.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();
        $io = new SymfonyStyle($input, $output);

        $downloader = $this->getContainer()->get('atarashii_api.communicator');
        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');
        $outputDir = $rootDir.'/../src/Atarashii/APIBundle/Tests/InputSamples';

        $io->title('Input Sample Updater');

        try {
            $this->fetchAnonPages($io, $fs, $downloader, $outputDir);
            $this->fetchAuthPages($io, $fs, $downloader, $outputDir);

            $io->success('Completed Update.');
        } catch (ClientException $e) {
            $response = $e->getResponse();

            if ($response->getStatusCode() === 404) {
                $io->error('Page doesn\'t exist anymore (404).');
            } else {
                throw $e;
            }
        } catch (IOException $e) {
            $io->error($e->getMessage());
        }
    }

    private function fetchAnonPages($io, $fs, $downloader, $outputDir)
    {
        $io->section('Fetching pages that don\'t need auth.');

        $io->progressStart(count(self::$anonPages));

        foreach (self::$anonPages as $page) {
            $pageTitle = $page[0];
            $pageUrl = $page[1];
            $pageFilename = $page[2];

            $io->progressAdvance();
            $io->text($pageTitle);
            $pageContent = $downloader->fetch($pageUrl);
            $fs->dumpFile($outputDir.$pageFilename, $pageContent);
        }
    }

    private function fetchAuthPages($io, $fs, $downloader, $outputDir)
    {
        $io->section('Fetching pages that require auth.');

        //Configured Username and Password for the Testing Account
        $credentials = $this->getContainer()->getParameter('unit_testing');
        $username = $credentials['username'];
        $password = $credentials['password'];
        $messageId = $credentials['message_id'];

        if ($username !== 'CHANGEME') {
            if ($downloader->cookieLogin($username, $password)) {
                $io->progressStart(count(self::$authPages));

                foreach (self::$authPages as $page) {
                    $pageTitle = $page[0];
                    $pageUrl = str_replace('__MESSAGEID__', $messageId, $page[1]);
                    $pageFilename = $page[2];

                    $io->progressAdvance();
                    $io->text($pageTitle);
                    $pageContent = $downloader->fetch($pageUrl);
                    $fs->dumpFile($outputDir.$pageFilename, $pageContent);
                }
            } else {
                $io->error('Username and password did not work. Please check that they are set correctly in the unit_testing parameters.');
            }
        } else {
            $io->note('Username and Password for user are not set. Cannot download updates for pages requiring auth.');
        }
    }
}
