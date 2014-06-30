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

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Until;

class Cast
{
    /**
     * The name of character|staff member
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $name;

    /**
     * The character role of the story
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $role;

    /**
     * The character|staff member image URL
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $image;

    /**
     * The staff member rank|influence inside the team
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $rank;

    /**
     * The voice actor name
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $actorName;

    /**
     * The voice actor image URL
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $actorImage;

    /**
     * The language of the voice actor
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $actorLanguage; //The language of the voice actor.

    /**
     * Set the name property
     *
     * @param string $name The character name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the name property.
     *
     * @return string
     */
    public function getName()
    {
       return $this->name;
    }

    /**
     * Set the role property
     *
     * @param string $role The character role
     *
     * @return void
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * Get the role property.
     *
     * @return string
     */
    public function getRole()
    {
       return $this->role;
    }

    /**
     * Set the image property
     *
     * @param string $image The character image.
     *
     * @return void
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * Get the image property.
     *
     * @return string
     */
    public function getImage()
    {
       return $this->image;
    }

    /**
     * Set the rank property
     *
     * @param string $rank The rank of the staff member.
     *
     * @return void
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
    }

    /**
     * Get the rank property
     *
     * @return string
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set the actorName property
     *
     * @param string $actorName The name of the voice actor.
     *
     * @return void
     */
    public function setActorName($actorName)
    {
        $this->actorName = $actorName;
    }

    /**
     * Get the actorName property
     *
     * @return string
     */
    public function getActorName()
    {
        return $this->actorName;
    }

    /**
     * Set the actorImage property
     *
     * @param string $actorImage The image of the voice actor.
     *
     * @return void
     */
    public function setActorImage($actorImage)
    {
        $this->actorImage = $actorImage;
    }

    /**
     * Get the actorImage property
     *
     * @return string
     */
    public function getActorImage()
    {
        return $this->actorImage;
    }

    /**
     * Set the actorLanguage property
     *
     * @param string $actorLanguage The language of the voice actor.
     *
     * @return void
     */
    public function setActorLanguage($actorLanguage)
    {
        $this->actorLanguage = $actorLanguage;
    }

    /**
     * Get the actorLanguage property
     *
     * @return string
     */
    public function getActorLanguage()
    {
        return $this->actorLanguage;
    }

}