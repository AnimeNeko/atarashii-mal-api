<?php
/**
* Atarashii MAL API
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014-2015 Ratan Dhawtal and Michael Johnson
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
     * @Type("integer")
     * @Since("2.0")
     */
    private $id;

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
     * The actors
     *
     * @Since("2.0")
     */
    private $actors;

    /**
     * Set the name property
     *
     * @param integer $id The character/staff id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the name property.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

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
     * Set the actors array property
     *
     * @param Actor $actors The actors.
     *
     * @return void
     */
    public function setActors($actors)
    {
        $this->actors[] = $actors;
    }

    /**
     * Get the actors property
     *
     * @return string
     */
    public function getActors()
    {
        return $this->actors;
    }
}