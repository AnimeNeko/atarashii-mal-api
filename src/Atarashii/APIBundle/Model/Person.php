<?php
/**
 * Atarashii MAL API.
 *
 * @author    Kyle Lanchman <k.lanchman@gmail.com>
 * @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
 * @author    Michael Johnson <youngmug@animeneko.net>
 * @copyright 2014-2016 Ratan Dhawtal and Michael Johnson
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
 */

namespace Atarashii\APIBundle\Model;

use JMS\Serializer\Annotation\Type;

class Person
{
    /**
     * The id of a person
     *
     * @Type("integer")
     */
    private $id;

    /**
     * The person image URL
     *
     * @Type("string")
     */
    private $imageUrl;

    /**
     * The person's name
     *
     * @Type("string")
     */
    private $name;

    /**
     * The person's alternate names
     *
     * @Type("array")
     */
    private $alternateNames = array();

    /**
     * The person's given name
     *
     * @Type("string")
     */
    private $givenName;

    /**
     * The person's family name
     *
     * @Type("string")
     */
    private $familyName;

    /**
     * The person's birthday
     *
     * This is the person's birthday, formatted as an ISO8601-compatible string.
     * The contents my be formatted as a year; month and day; or year, month and day.
     * The value is null if unknown
     * Examples: "1989-02-25", "02-25", or "1989"
     *
     * @Type("string")
     */
    private $birthday;

    /**
     * The person's personal website URL
     *
     * @Type("string")
     */
    private $websiteUrl;

    /**
     * More details about the person
     *
     * An HTML-formatted string with more details about the person
     *
     * @Type("string")
     */
    private $moreDetails;

    /**
     * The number of members that have the person marked as a favorite
     *
     * @Type("integer")
     */
    private $favoritedCount;

    /**
     * Voice acting roles a person has had
     *
     * @Type("array")
     */
    private $voiceActingRoles = array();

    /**
     * Anime staff positions a person has had
     *
     * @Type("array")
     */
    private $animeStaffPositions = array();

    /**
     * Manga that a person has had published
     *
     * @Type("array")
     */
    private $publishedManga = array();

    /**
     * Get the id property
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id property
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the imageUrl property
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Set the imageUrl property
     * @param string $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        if ($imageUrl === 'http://cdn.myanimelist.net/images/questionmark_23.gif') {
            $this->imageUrl = 'http://cdn.myanimelist.net/images/na.gif';
        } else {
            $this->imageUrl = $imageUrl;
        }
    }

    /**
     * Get the name property
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name property
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the alternateNames property
     * @return array
     */
    public function getAlternateNames()
    {
        return $this->alternateNames;
    }

    /**
     * Set the alternateNames property
     * @param array $alternateNames
     */
    public function setAlternateNames($alternateNames)
    {
        $this->alternateNames = $alternateNames;
    }

    /**
     * Get the givenName property
     * @return string
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * Set the givenName property
     * @param string $givenName
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
    }

    /**
     * Get the familyName property
     * @return string
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * Set the familyName property
     * @param string $familyName
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;
    }

    /**
     * Get the birthday property
     * @return string
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set the startDate2 property.
     *
     * @param \DateTime $birthday The start date of the series
     * @param string    $accuracy  To what level of accuracy this item is. May be "year", "month", "dayMonth", or "day".
     *                             Defaults to "day".
     */
    public function setBirthday(\DateTime $birthday, $accuracy = 'day')
    {
        switch ($accuracy) {
            case 'year':
                $this->birthday = $birthday->format('Y');
                break;
            case 'month':
                $this->birthday = $birthday->format('Y-m');
                break;
            case 'dayMonth':
                $this->birthday = $birthday->format('-m-d');
                break;
            case 'day':
            default:
                $this->birthday = $birthday->format('Y-m-d');
        }
    }

    /**
     * Get the websiteUrl property
     * @return string
     */
    public function getWebsiteUrl()
    {
        return $this->websiteUrl;
    }

    /**
     * Set the websiteUrl property
     * @param string $websiteUrl
     */
    public function setWebsiteUrl($websiteUrl)
    {
        $this->websiteUrl = $websiteUrl;
    }

    /**
     * Get the moreDetails property
     * @return string
     */
    public function getMoreDetails()
    {
        return $this->moreDetails;
    }

    /**
     * Set the moreDetails property
     * @param string $moreDetails
     */
    public function setMoreDetails($moreDetails)
    {
        $this->moreDetails = $moreDetails;
    }

    /**
     * Get the favoritedCount property
     * @return int
     */
    public function getFavoritedCount()
    {
        return $this->favoritedCount;
    }

    /**
     * Set the favoritedCount property
     * @param int $favoritedCount
     */
    public function setFavoritedCount($favoritedCount)
    {
        $this->favoritedCount = $favoritedCount;
    }

    /**
     * Get the voiceActingRoles property
     * @return array
     */
    public function getVoiceActingRoles()
    {
        return $this->voiceActingRoles;
    }

    /**
     * Set the voiceActingRoles property
     * @param array $voiceActingRoles
     */
    public function setVoiceActingRoles($voiceActingRoles)
    {
        $this->voiceActingRoles = $voiceActingRoles;
    }

    /**
     * Get the animeStaffPositions property
     * @return array
     */
    public function getAnimeStaffPositions()
    {
        return $this->animeStaffPositions;
    }

    /**
     * Set the animeStaffPositions property
     * @param array $animeStaffPositions
     */
    public function setAnimeStaffPositions($animeStaffPositions)
    {
        $this->animeStaffPositions = $animeStaffPositions;
    }

    /**
     * Get the publishedManga property
     * @return array
     */
    public function getPublishedManga()
    {
        return $this->publishedManga;
    }

    /**
     * Set the publishedManga property
     * @param array $publishedManga
     */
    public function setPublishedManga($publishedManga)
    {
        $this->publishedManga = $publishedManga;
    }
}
