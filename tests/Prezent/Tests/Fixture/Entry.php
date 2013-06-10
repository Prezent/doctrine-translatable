<?php

namespace Prezent\Tests\Fixture;

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
     * @ORM\OneToMany(targetEntity="Prezent\Tests\Fixture\EntryTranslation", mappedBy="object")
     * @Prezent\Translations
     */
    private $translations;

    public function getName()
    {
        return $this->currentTranslation->getName();
    }
    
    public function setName($name)
    {
        $this->currentTranslation->setName($name);
        return $this;
    }
}
