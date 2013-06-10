<?php

namespace Prezent\Tests\Fixture;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;

/**
 * @ORM\Entity
 */
class Entry
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
     * @ORM\OneToMany(targetEntity="Prezent\Tests\Fixture\EntryTranslation", mappedBy="object", cascade={"persist"})
     * @Prezent\Translations
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
    
    public function addTranslation(EntryTranslation $translation)
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setObject($this);
        }
    
        return $this;
    }
    
    public function removeTranslation(EntryTranslation $translation)
    {
        if ($this->translations->removeElement($translation)) {
            $translation->setObject(null);
        }
    
        return $this;
    }
}
