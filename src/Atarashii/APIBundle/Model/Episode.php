<?php
/**
* Atarashii MAL API.
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014-2016 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/
namespace Atarashii\APIBundle\Model;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Until;
use Atarashii\APIBundle\Helper\Date;

class Episode
{
    /**
     * The episode number.
     *
     * @Type("integer")
     */
    private $number;

    /**
     * Title of the anime.
     *
     * @Type("string")
     */
    private $title;

    /**
     * Map of other titles for the anime.
     *
     * @Type("array<string, array<string>>")
     */
    private $otherTitles = array();

    /**
     * Airing date from which this anime was be aired.
     *
     * Example: "Thu Oct 07 22:28:07 -0700 2004" or "2004".
     *
     * @Type("string")
     */
    private $airDate;

    /**
     * Set the episode number property.
     *
     * @param int $number The episode number.
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * Get the number property.
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set the title property.
     *
     * @param string $title The title of series.
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get the title property.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the otherTitles property.
     *
     * @param array $otherTitles Other titles of series.
     */
    public function setOtherTitles($otherTitles)
    {
        $this->otherTitles = $otherTitles;
    }

    /**
     * Get the otherTitles property.
     *
     * @return array
     */
    public function getOtherTitles()
    {
        return $this->otherTitles;
    }

    /**
     * Set the airDate property.
     *
     * @param \DateTime $airDate The airing date of the series
     * @param string    $accuracy  To what level of accuracy this item is. May be "year", "month", or "day". Defaults to "day".
     */
    public function setAirDate(\DateTime $airDate, $accuracy = 'day')
    {
        switch ($accuracy) {
            case 'year':
                $this->airDate = $airDate->format('Y');
                break;
            case 'month':
                $this->airDate = $airDate->format('Y-m');
                break;
            case 'day':
            default:
                $this->airDate = $airDate->format('Y-m-d');
        }
    }

    /**
     * Get the startDate property.
     *
     * @return string (ISO 8601)
     */
    public function getAirDate()
    {
        return $this->airDate;
    }
}
