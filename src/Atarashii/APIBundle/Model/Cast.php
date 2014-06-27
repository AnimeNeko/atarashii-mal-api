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

class Cast
{
    private $name; //The creation name or The staff member name.
    private $role; //The anime role.
    private $image; //The image of the character or staff member.
    private $rank; //The staff member rank.
    private $actor_name; //The actor name
    private $actor_image; //The image of the voice actor.
    private $actor_language; //The language of the voice actor.

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
     * Set the actor_name property
     *
     * @param string $actor_name The name of the voice actor.
     *
     * @return void
     */
    public function setActorName($actor_name)
    {
        $this->actor_name = $actor_name;
    }

    /**
     * Get the actor_name property
     *
     * @return string
     */
    public function getActorName()
    {
        return $this->actor_name;
    }

    /**
     * Set the actor_image property
     *
     * @param string $actor_image The image of the voice actor.
     *
     * @return void
     */
    public function setActorImage($actor_image)
    {
        $this->actor_image = $actor_image;
    }

    /**
     * Get the actor_image property
     *
     * @return string
     */
    public function getActorImage()
    {
        return $this->actor_image;
    }

    /**
     * Set the Actor_language property
     *
     * @param string $actor_language The language of the voice actor.
     *
     * @return void
     */
    public function setActorLanguage($actor_language)
    {
        $this->actor_language = $actor_language;
    }

    /**
     * Get the Actor_language property
     *
     * @return string
     */
    public function getActorLanguage()
    {
        return $this->actor_language;
    }

}