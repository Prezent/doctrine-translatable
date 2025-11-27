<?php

namespace Prezent\Tests\Fixture;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Attribute as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

#[ORM\Entity]
class Inherited extends AbstractTranslatable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: 'integer')]
    protected $id;

    #[Prezent\Translations(targetEntity: InheritedTranslation::class)]
    protected $translations;

    #[Prezent\CurrentLocale]
    public $currentLocale;

    #[Prezent\FallbackLocale]
    public $fallbackLocale;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }
}
