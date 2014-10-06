<?php
/**
* Atarashii MAL API
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/

namespace Atarashii\APIBundle\Model;

use \DateTime;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Until;

class Date
{

    /**
     * Format the time in ISO 8601.
     *
     * Can be changed into another format by using the php Date characters.
     * Read the documentation for more info: http://php.net/manual/en/function.date.php
     *
     * @param string $time The time that needs to be standardized.
     *
     * @return string
     */
    public static function formatTime($time)
    {
        if (strpos($time, '-') !== false) {
            return DateTime::createFromFormat('m-d-y, g:i A', $time)->format(DateTime::ISO8601);
        } else if (strpos($time, 'Now') !== false) {
            return (new DateTime())->format(DateTime::ISO8601);
        } else if (strpos($time, 'seconds') !== false) {
            return (new DateTime())->modify('-' . substr($time, 0, -12) . ' second')->format(DateTime::ISO8601);
        } else if (strpos($time, 'minutes') !== false) {
            return (new DateTime())->modify('-' . substr($time, 0, -12) . ' minute')->format('Y-m-d\TH:iO');
        } else if (strpos($time, 'minute') !== false) {
            return (new DateTime())->modify('-' . substr($time, 0, -11) . ' minute')->format('Y-m-d\TH:iO');
        } else if (strpos($time, 'hours') !== false) {
            return (new DateTime())->modify('-' . substr($time, 0, -10) . ' hour')->format('Y-m-d\THO');
        } else if (strpos($time, 'hour') !== false) {
            return (new DateTime())->modify('-' . substr($time, 0, -9) . ' hour')->format('Y-m-d\THO');
        } else if (strpos($time, 'Today') !== false) {
            return DateTime::createFromFormat('g:i A', substr($time, 7))->format(DateTime::ISO8601);
        } else if (strpos($time, 'Yesterday') !== false) {
            return DateTime::createFromFormat('g:i A', substr($time, 11))->modify('-1 day')->format(DateTime::ISO8601);
        } else if (strpos($time, ', ') !== false) { //Do not place this before the other formatters because it will break almost all dates.
            return DateTime::createFromFormat('F d, Y', $time)->format(DateTime::ISO8601);
        } else if (strpos($time, ' ') !== false) { //Do not place this before the other formatters because it will break almost all dates.
            return DateTime::createFromFormat('M Y', $time)->format('Y-m');
        } else {
            return null;
        }
    }

}