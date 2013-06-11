<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\Entity;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Translatable;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractTranslation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * Mapping must be supplied by the implementation class
     */
    protected $object;

    /**
     * @ORM\Column(name="locale", type="string")
     */
    protected $locale;

    /**
     * Getter for id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the object
     *
     * @return Translatable
     */
    public function getObject()
    {
        return $this->object;
    }
    
    /**
     * Set the object
     *
     * @param Translatable $object
     * @return self
     */
    public function setObject(Translatable $object = null)
    {
        if ($this->object == $object) {
            return $this;
        }
    
        $old = $this->object;
        $this->object = $object;
    
        if ($old !== null) {
            $old->removeTranslation($this);
        }
    
        if ($object !== null) {
            $object->addTranslation($this);
        }
    
        return $this;
    }

    /**
     * Get the locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the locale
     *
     * @param string $locale
     * @return self
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }
}
