<?php
/**
* Atarashii MAL API
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014-2015 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/

namespace Atarashii\APIBundle\Helper;

use \DateTime;
use \DateTimeZone;
use Symfony\Component\DomCrawler\Crawler;

class Date
{
    Public static $timeZone = 'America/Los_Angeles';

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
        $dateTime = (new DateTime);
        $timeZone = new DateTimeZone(Date::$timeZone);
        $time = trim($time);

        if (strpos($time, '-') !== false) {
            return $dateTime->createFromFormat('m-d-y, g:i A', $time, $timeZone)->format('Y-m-d\TH:iO');
        } else if (strpos($time, 'Now') !== false) {
            return $dateTime->format(DateTime::ISO8601);
        } else if (strpos($time, 'seconds') !== false) {
            return $dateTime->modify('-' . substr($time, 0, -12) . ' second')->format(DateTime::ISO8601);
        } else if (strpos($time, 'minutes') !== false) {
            return $dateTime->modify('-' . substr($time, 0, -12) . ' minute')->format('Y-m-d\TH:iO');
        } else if (strpos($time, 'minute') !== false) {
            return $dateTime->modify('-' . substr($time, 0, -11) . ' minute')->format('Y-m-d\TH:iO');
        } else if (strpos($time, 'hours') !== false) {
            return $dateTime->modify('-' . substr($time, 0, -10) . ' hour')->format('Y-m-d\THO');
        } else if (strpos($time, 'hour') !== false) {
            return $dateTime->modify('-' . substr($time, 0, -9) . ' hour')->format('Y-m-d\THO');
        } else if (strpos($time, 'Today') !== false) {
            return $dateTime->createFromFormat('g:i A', substr($time, 7), $timeZone)->format('Y-m-d\TH:iO');
        } else if (strpos($time, 'Yesterday') !== false) {
            return $dateTime->createFromFormat('g:i A', substr($time, 11), $timeZone)->modify('-1 day')->format('Y-m-d\TH:iO');
        } else if (strpos($time, 'AM') !== false || strpos($time, 'PM') !== false) {
            if (strlen($time) > 18)
                return $dateTime->createFromFormat('M j, Y g:i A', $time, $timeZone)->format('Y-m-d\TH:iO');
            else
                return $dateTime->createFromFormat('M j, g:i A', $time, $timeZone)->format('Y-m-d\TH:iO');
        } else if (strpos($time, ', ') !== false) { //Do not place this before the other formatters because it will break almost all dates.
            if (strlen($time) > 12)
                return $dateTime->createFromFormat('F d, Y', $time)->format('Y-m-d');
            else
                return $dateTime->createFromFormat('M j, Y', $time)->format('Y-m-d');
        } else if (strpos($time, ' ') !== false) { //Do not place this before the other formatters because it will break almost all dates.
            return $dateTime->createFromFormat('M Y', $time)->format('Y-m');
        } else {
            return null;
        }
    }

    /**
     * Set timezone setting by parsing the MAL settings
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

        Date::$timeZone = $crawler->filter('option[selected]')->text();
    }
}