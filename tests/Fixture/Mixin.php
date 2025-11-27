<?php

namespace Prezent\Tests\Fixture;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Attribute as Prezent;
use Prezent\Doctrine\Translatable\TranslatableInterface;
use Prezent\Doctrine\Translatable\Entity\TranslatableTrait;

#[ORM\Entity]
class Mixin implements TranslatableInterface
{
    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: 'integer')]
    protected $id;

    #[Prezent\Translations(targetEntity: MixinTranslation::class)]
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
