<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\Entity;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Translatable;
use Prezent\Doctrine\Translatable\Translation;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractTranslation implements Translation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * Mapping provided by implementation
     */
    protected $translatable;

    /**
     * @ORM\Column(name="locale", type="string")
     * @Prezent\Locale
     */
    protected $locale;

    /**
     * Get the ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the translatable object
     *
     * @return Translatable
     */
    public function getTranslatable()
    {
        return $this->translatable;
    }
    
    /**
     * Set the translatable object
     *
     * @param Translatable $translatable
     * @return self
     */
    public function setTranslatable(Translatable $translatable = null)
    {
        if ($this->translatable == $translatable) {
            return $this;
        }
    
        $old = $this->translatable;
        $this->translatable = $translatable;
    
        if ($old !== null) {
            $old->removeTranslation($this);
        }
    
        if ($translatable !== null) {
            $translatable->addTranslation($this);
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
