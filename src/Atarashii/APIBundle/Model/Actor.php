<?php
/**
* Atarashii MAL API.
*
* @author    Ratan Dhawtal <ratandhawtal@hotmail.com>
* @author    Michael Johnson <youngmug@animeneko.net>
* @copyright 2014-2015 Ratan Dhawtal and Michael Johnson
* @license   http://www.apache.org/licenses/LICENSE-2.0 Apache Public License 2.0
*/
namespace Atarashii\APIBundle\Model;

use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;

class Actor
{
    /**
     * The id of an actor.
     *
     * @Type("integer")
     * @Since("2.0")
     */
    private $id;

    /**
     * The voice actor name.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $name;

    /**
     * The voice actor image URL.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $image;

    /**
     * The language of the voice actor.
     *
     * @Type("string")
     * @Since("2.0")
     */
    private $language;

    /**
     * Set the id property.
     *
     * @param int $id The actor
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
     * Set the actorName property.
     *
     * @param string $actorName The name of the voice actor.
     */
    public function setName($actorName)
    {
        $this->name = $actorName;
    }

    /**
     * Get the actorName property.
     *
     * @return string
     */
    public function getActorName()
    {
        return $this->name;
    }

    /**
     * Set the actorImage property.
     *
     * @param string $actorImage The image of the voice actor.
     */
    public function setImage($actorImage)
    {
        if ($actorImage === 'http://cdn.myanimelist.net/images/questionmark_23.gif') {
            $this->image = 'http://cdn.myanimelist.net/images/na.gif';
        } else {
            $this->image = $actorImage;
        }
    }

    /**
     * Get the actorImage property.
     *
     * @return string
     */
    public function getActorImage()
    {
        return $this->image;
    }

    /**
     * Set the actorLanguage property.
     *
     * @param string $actorLanguage The language of the voice actor.
     */
    public function setLanguage($actorLanguage)
    {
        $this->language = $actorLanguage;
    }

    /**
     * Get the actorLanguage property.
     *
     * @return string
     */
    public function getActorLanguage()
    {
        return $this->language;
    }
}
