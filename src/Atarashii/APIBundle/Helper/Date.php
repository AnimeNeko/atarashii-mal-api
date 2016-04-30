<?php
/**
* Atarashii MAL API.
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014-2016 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/
namespace Atarashii\APIBundle\Helper;

use DateTime;
use DateTimeZone;
use Symfony\Component\DomCrawler\Crawler;

class Date
{
    public static $timeZone = 'America/Los_Angeles';

    /**
     * Format the time in ISO 8601.
     *
     * Can be changed into another format by using the php Date characters.
     * Read the documentation for more info: http://php.net/manual/en/function.date.php
     *
     * The timezone is only used when there is at least a hour in the $time parameter.
     * Using the timezone will result in 1 day difference depending on the server location.
     *
     * @param string $time The time that needs to be standardized.
     *
     * @return string
     */
    public static function formatTime($time)
    {
        $dateTime = (new DateTime());
        $timeZone = new DateTimeZone(self::$timeZone);
        $time = trim($time);

        if (strpos($time, '-') !== false) {
            return $dateTime->createFromFormat('m-d-y, g:i A', $time, $timeZone)->format('Y-m-d\TH:iO');
        } elseif (strpos($time, 'Now') !== false) {
            return $dateTime->format(DateTime::ISO8601);
        } elseif (strpos($time, 'seconds') !== false) {
            return $dateTime->modify('-'.substr($time, 0, -12).' second')->format(DateTime::ISO8601);
        } elseif (strpos($time, 'minutes') !== false) {
            return $dateTime->modify('-'.substr($time, 0, -12).' minute')->format('Y-m-d\TH:iO');
        } elseif (strpos($time, 'minute') !== false) {
            return $dateTime->modify('-'.substr($time, 0, -11).' minute')->format('Y-m-d\TH:iO');
        } elseif (strpos($time, 'hours') !== false) {
            return $dateTime->modify('-'.substr($time, 0, -10).' hour')->format('Y-m-d\THO');
        } elseif (strpos($time, 'hour') !== false) {
            return $dateTime->modify('-'.substr($time, 0, -9).' hour')->format('Y-m-d\THO');
        } elseif (strpos($time, 'Today') !== false) {
            return $dateTime->createFromFormat('g:i A', substr($time, 7), $timeZone)->format('Y-m-d\TH:iO');
        } elseif (strpos($time, 'Yesterday') !== false) {
            return $dateTime->createFromFormat('g:i A', substr($time, 11), $timeZone)->modify('-1 day')->format('Y-m-d\TH:iO');
        } elseif (strpos($time, 'AM') !== false || strpos($time, 'PM') !== false) {
            if (strlen($time) > 18) {
                return $dateTime->createFromFormat('M j, Y g:i A', $time, $timeZone)->format('Y-m-d\TH:iO');
            } else {
                return $dateTime->createFromFormat('M j, g:i A', $time, $timeZone)->format('Y-m-d\TH:iO');
            }
        } elseif (strpos($time, ', ') !== false) { //Do not place this before the other formatters because it will break almost all dates.
            if (strlen($time) > 12) {
                return $dateTime->createFromFormat('F d, Y', $time)->format('Y-m-d');
            } else {
                return $dateTime->createFromFormat('M j, Y', $time)->format('Y-m-d');
            }
        } elseif (strpos($time, '/') !== false) {
            return $dateTime->createFromFormat('m/d/Y H:i', $time, $timeZone)->format('Y-m-d\TH:iO');
        } elseif (strpos($time, ':') !== false) {
            return $dateTime->createFromFormat('l H:i T', $time)->format('Y-m-d\TH:iO');
        } elseif (strpos($time, ' ') !== false && (strlen($time) === 6 || strlen($time) === 5)) { //Do not place this before the other formatters because it will break almost all dates.
            return $dateTime->createFromFormat('M j Y', $time.' '.date("Y"))->format('Y-m-d');
        } elseif (strpos($time, ' ') !== false) { //Do not place this before the other formatters because it will break almost all dates.
            //WARNING: PHP will fill in missing details with the current date. This can be a problem when processing
            //a month with fewer days than the current date (e.g. April, which has 30 days, on the 31st of another month).
            //To solve this, use a fake day in the input, but not the output so we return the correct date.
            $date = $dateTime->createFromFormat('M Y d', $time.' 01');

            if ($date !== false) {
                return $date->format('Y-m');
            }
        }

        //All else has failed, just return
        return;
    }

    /**
     * Set timezone setting by parsing the MAL settings.
     *
     * Parse the timezone setting used by MAL when an user logged in.
     * This depends on the location of the country when a user joined.
     * After parsing it stores the timezone in a static variable.
     *
     * @param string $settings The HTML source  of the settings page that contains the timezone.
     *
     * @return string
     */
    public static function setTimeZone($settings)
    {
        $crawler = new Crawler();
        $crawler->addHTMLContent($settings, 'UTF-8');

        self::$timeZone = $crawler->filter('option[selected]')->text();
    }
}
