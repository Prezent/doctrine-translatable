<?php

namespace Prezent\Tests\Fixture;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Translatable;
use Prezent\Doctrine\Translatable\Translation;

/**
 * @ORM\Entity
 */
class Mapped implements Translatable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @Prezent\CurrentTranslation
     * @Prezent\FallbackTranslation
     */
    private $currentTranslation;

    /**
     * @Prezent\Translations(targetEntity="Prezent\Tests\Fixture\MappedTranslation")
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getName()
    {
        return $this->currentTranslation->getName();
    }
    
    public function setName($name)
    {
        $this->currentTranslation->setName($name);
        return $this;
    }

    public function getCurrentTranslation()
    {
        return $this->currentTranslation;
    }

    public function getTranslations()
    {
        return $this->translations;
    }
    
    public function addTranslation(Translation $translation)
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setTranslatable($this);
        }
    
        return $this;
    }
    
    public function removeTranslation(Translation $translation)
    {
        if ($this->translations->removeElement($translation)) {
            $translation->setTranslatable(null);
        }
    
        return $this;
    }
}
