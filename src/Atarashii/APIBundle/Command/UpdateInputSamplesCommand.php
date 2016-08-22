<?php
/**
* Atarashii MAL API.
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014-2016 Ratan Dhawtal and Michael Johnson
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
        $outputDir = $rootDir . '/../src/Atarashii/ApiBundle/Tests/InputSamples';

        $io->title('Input Sample Updater');

        try {
            $this->fetchAnonPages($io, $fs, $downloader, $outputDir);
            $this->fetchAuthPages($io, $fs, $downloader, $outputDir);

            $io->success('Completed Update.');
        } catch (ClientException $e) {
            $response = $e->getResponse();

            if ($response->getStatusCode() == 404) {
                $io->error('Page doesn\'t exist anymore (404).');
            } else {
                throw $e;
            }
        } catch (IOException $e) {
            $io->error($e->getMessage());
        }
    }

    private function fetchAnonPages($io, $fs, $downloader, $outputDir) {
        $io->section('Fetching pages that don\'t need auth.');

        $io->progressStart(31);

        $io->progressAdvance();
        $io->text('Anime 10236 (Kagee Grimm Douwa)');
        $pageContent = $downloader->fetch('/anime/10236');
        $fs->dumpFile($outputDir . '/anime-10236.html', $pageContent);

        $io->progressAdvance();
        $io->text('Manga 137 (R.O.D: Read or Die)');
        $pageContent = $downloader->fetch('/manga/137');
        $fs->dumpFile($outputDir . '/manga-137.html', $pageContent);

        $io->progressAdvance();
        $io->text('Manga 44347 (One-Punch Man)');
        $pageContent = $downloader->fetch('/manga/44347');
        $fs->dumpFile($outputDir . '/manga-44347.html', $pageContent);

        $io->progressAdvance();
        $io->text('Reviews for Anime 1887 (Lucky Star)');
        $pageContent = $downloader->fetch('/anime/1887/_/reviews');
        $fs->dumpFile($outputDir . '/anime-1887-reviews.html', $pageContent);

        $io->progressAdvance();
        $io->text('Reviews for Manga 11977 (Bambino!)');
        $pageContent = $downloader->fetch('/manga/11977/_/reviews');
        $fs->dumpFile($outputDir . '/manga-11977-reviews.html', $pageContent);

        $io->progressAdvance();
        $io->text('Recommendations for Anime 21 (One Piece)');
        $pageContent = $downloader->fetch('/anime/21/_/userrecs');
        $fs->dumpFile($outputDir . '/anime-21-recs.html', $pageContent);

        $io->progressAdvance();
        $io->text('Recommendations for Manga 21 (Death Note)');
        $pageContent = $downloader->fetch('/manga/21/_/userrecs');
        $fs->dumpFile($outputDir . '/manga-21-recs.html', $pageContent);

        $io->progressAdvance();
        $io->text('Characters for Anime 1887 (Lucky Star)');
        $pageContent = $downloader->fetch('/anime/1887/_/characters');
        $fs->dumpFile($outputDir . '/anime-1887-cast.html', $pageContent);

        $io->progressAdvance();
        $io->text('Episodes for Anime 21 (One Piece)');
        $pageContent = $downloader->fetch('/anime/21/_/episode');
        $fs->dumpFile($outputDir . '/anime-21-eps.html', $pageContent);

        $io->progressAdvance();
        $io->text('Top Anime List');
        $pageContent = $downloader->fetch('/topanime.php?limit=0');
        $fs->dumpFile($outputDir . '/anime-top.html', $pageContent);

        $io->progressAdvance();
        $io->text('Top Manga List');
        $pageContent = $downloader->fetch('/topmanga.php?limit=0');
        $fs->dumpFile($outputDir . '/manga-top.html', $pageContent);

        $io->progressAdvance();
        $io->text('Upcoming Anime List for January 1930');
        $pageContent = $downloader->fetch('/anime.php?sd=01&sm=01&sy=1930&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show=0');
        $fs->dumpFile($outputDir . '/anime-upcoming-1930.html', $pageContent);

        $io->progressAdvance();
        $io->text('Upcoming Anime List for June 2016');
        $pageContent = $downloader->fetch('/anime.php?sd=09&sm=06&sy=2016&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show=0');
        $fs->dumpFile($outputDir . '/anime-upcoming.html', $pageContent);

        $io->progressAdvance();
        $io->text('Upcoming Manga List for June 2016');
        $pageContent = $downloader->fetch('/manga.php?sd=09&sm=06&sy=2016&em=0&ed=0&ey=0&o=2&w=&c[]=a&c[]=d&c[]=a&c[]=b&c[]=c&c[]=d&c[]=e&c[]=f&c[]=g&cv=1&show=0');
        $fs->dumpFile($outputDir . '/manga-upcoming.html', $pageContent);

        $io->progressAdvance();
        $io->text('Anime Season Schedule');
        $pageContent = $downloader->fetch('/anime/season/schedule');
        $fs->dumpFile($outputDir . '/schedule-26052016.html', $pageContent);

        $io->progressAdvance();
        $io->text('Forum Index');
        $pageContent = $downloader->fetch('/forum/');
        $fs->dumpFile($outputDir . '/forum-index.html', $pageContent);

        $io->progressAdvance();
        $io->text('Forum Board 14 (MAL Guidelines & FAQ)');
        $pageContent = $downloader->fetch('/forum/?board=14');
        $fs->dumpFile($outputDir . '/forum-board-14.html', $pageContent);

        $io->progressAdvance();
        $io->text('Forum Sub-Board 2 (Anime DB)');
        $pageContent = $downloader->fetch('/forum/?subboard=2');
        $fs->dumpFile($outputDir . '/forum-sub-2.html', $pageContent);

        $io->progressAdvance();
        $io->text('Forum Topic 516059 (Site & Forum Guidelines)');
        $pageContent = $downloader->fetch('/forum/?topicid=516059');
        $fs->dumpFile($outputDir . '/forum-topic-516059.html', $pageContent);

        $io->progressAdvance();
        $io->text('Person 10 (Bosch, Johnny Yong)');
        $pageContent = $downloader->fetch('/people/10');
        $fs->dumpFile($outputDir . '/people-10.html', $pageContent);

        $io->progressAdvance();
        $io->text('Person 159 (Rial, Monica)');
        $pageContent = $downloader->fetch('/people/159');
        $fs->dumpFile($outputDir . '/people-159.html', $pageContent);

        $io->progressAdvance();
        $io->text('Person 185 (Hanazawa, Kana)');
        $pageContent = $downloader->fetch('/people/185');
        $fs->dumpFile($outputDir . '/people-185.html', $pageContent);

        $io->progressAdvance();
        $io->text('Person 1870 (Miyazaki, Hayao)');
        $pageContent = $downloader->fetch('/people/1870');
        $fs->dumpFile($outputDir . '/people-1870.html', $pageContent);

        $io->progressAdvance();
        $io->text('Person 2608 (Miyamoto, Kano)');
        $pageContent = $downloader->fetch('/people/2608');
        $fs->dumpFile($outputDir . '/people-2608.html', $pageContent);

        $io->progressAdvance();
        $io->text('Person 7277 (Ikimono-gakari)');
        $pageContent = $downloader->fetch('/people/7277');
        $fs->dumpFile($outputDir . '/people-7277.html', $pageContent);

        $io->progressAdvance();
        $io->text('Person 10145 (huke)');
        $pageContent = $downloader->fetch('/people/10145');
        $fs->dumpFile($outputDir . '/people-10145.html', $pageContent);

        $io->progressAdvance();
        $io->text('Person 11746 (ClariS)');
        $pageContent = $downloader->fetch('/people/11746');
        $fs->dumpFile($outputDir . '/people-11746.html', $pageContent);

        $io->progressAdvance();
        $io->text('Profile for User Xinil');
        $pageContent = $downloader->fetch('/profile/xinil');
        $fs->dumpFile($outputDir . '/profile-xinil.html', $pageContent);

        $io->progressAdvance();
        $io->text('Profile for User Motoko');
        $pageContent = $downloader->fetch('/profile/motoko');
        $fs->dumpFile($outputDir . '/profile-motokochan.html', $pageContent);

        $io->progressAdvance();
        $io->text('Friendlist for User Motoko');
        $pageContent = $downloader->fetch('/profile/motoko/friends');
        $fs->dumpFile($outputDir . '/profile-motokochan-friends.html', $pageContent);

        $io->progressAdvance();
        $io->text('History for User Motoko');
        $pageContent = $downloader->fetch('/history/motoko');
        $fs->dumpFile($outputDir . '/history-motokochan.html', $pageContent);
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
                $io->progressStart(10);

                $io->progressAdvance();
                $io->text('Anime 1887 Personal (Lucky Star)');
                $pageContent = $downloader->fetch('/anime/1887');
                $fs->dumpFile($outputDir . '/anime-1887-mine.html', $pageContent);

                $io->progressAdvance();
                $io->text('Manga 11977 Personal (Bambino!)');
                $pageContent = $downloader->fetch('/manga/11977');
                $fs->dumpFile($outputDir . '/manga-11977-mine.html', $pageContent);

                $io->progressAdvance();
                $io->text('Anime 1689 Personal Details (5cm/s)');
                $pageContent = $downloader->fetch('/ownlist/anime/1689/edit?hideLayout');
                $fs->dumpFile($outputDir . '/anime-1689-mine-detailed.html', $pageContent);

                $io->progressAdvance();
                $io->text('Manga 11977 Personal Details (Bambino!)');
                $pageContent = $downloader->fetch('/ownlist/manga/11977/edit?hideLayout');
                $fs->dumpFile($outputDir . '/manga-11977-mine-detailed.html', $pageContent);

                $io->progressAdvance();
                $io->text('Manga 17074 Personal Details (We Can Fly!)');
                $pageContent = $downloader->fetch('/ownlist/manga/17074/edit?hideLayout');
                $fs->dumpFile($outputDir . '/manga-17074-mine-detailed.html', $pageContent);

                $io->progressAdvance();
                $io->text('Anime 1689 History (5cm/s)');
                $pageContent = $downloader->fetch('/ajaxtb.php?detailedaid=1689');
                $fs->dumpFile($outputDir . '/anime-1689-history.html', $pageContent);

                $io->progressAdvance();
                $io->text('Manga 11977 History (Bambino!)');
                $pageContent = $downloader->fetch('/ajaxtb.php?detailedmid=11977');
                $fs->dumpFile($outputDir . '/manga-11977-history.html', $pageContent);

                $io->progressAdvance();
                $io->text('Account Profile Details (' . $username . ')');
                $pageContent = $downloader->fetch('/editprofile.php');
                $fs->dumpFile($outputDir . '/editprofile.html', $pageContent);

                $io->progressAdvance();
                $io->text('User Messages List');
                $pageContent = $downloader->fetch('/mymessages.php');
                $fs->dumpFile($outputDir . '/user-messages-list.html', $pageContent);

                $io->progressAdvance();
                $io->text('Message #' . $messageId);
                $pageContent = $downloader->fetch('/mymessages.php?go=read&id=' . $messageId);
                $fs->dumpFile($outputDir . '/user-message.html', $pageContent);
            } else {
                $io->error('Username and password did not work. Please check that they are set correctly in the unit_testing parameters.');
            }
        } else {
            $io->note('Username and Password for user are not set. Cannot download updates for pages requiring auth.');
        }
    }

    }
