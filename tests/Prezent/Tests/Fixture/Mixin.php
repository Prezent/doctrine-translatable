<?php

namespace Prezent\Tests\Fixture;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\TranslatableInterface;
use Prezent\Doctrine\Translatable\Entity\TranslatableTrait;

/**
 * @ORM\Entity
 */
class Mixin implements TranslatableInterface
{
    use TranslatableTrait;

    /**
     * @Prezent\Translations(targetEntity="Prezent\Tests\Fixture\MixinTranslation")
     */
    protected $translations;

    /**
     * @Prezent\CurrentLocale
     */
    public $currentLocale;

    /**
     * @Prezent\FallbackLocale
     */
    public $fallbackLocale;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }
}
