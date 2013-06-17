<?php

namespace Prezent\Tests\Fixture;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

/**
 * @ORM\Entity
 */
class Inherited extends AbstractTranslatable
{
    /**
     * @Prezent\Translations(targetEntity="Prezent\Tests\Fixture\InheritedTranslation")
     */
    protected $translations;

    /**
     * @Prezent\CurrentTranslation
     * @Prezent\FallbackTranslation
     */
    private $currentTranslation;

    /**
     * Constructor
     */
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
}
