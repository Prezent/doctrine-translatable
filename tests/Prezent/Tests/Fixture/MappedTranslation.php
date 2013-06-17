<?php

namespace Prezent\Tests\Fixture;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Translatable;
use Prezent\Doctrine\Translatable\Translation;

/**
 * @ORM\Entity
 */
class MappedTranslation implements Translation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @Prezent\Translatable(targetEntity="Prezent\Tests\Fixture\Mapped")
     */
    private $translatable;

    /**
     * @Prezent\Locale
     */
    private $locale;

    /**
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    public function getId()
    {
        return $this->id;
    }

    public function getTranslatable()
    {
        return $this->translatable;
    }
    
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

    public function getLocale()
    {
        return $this->locale;
    }
    
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
