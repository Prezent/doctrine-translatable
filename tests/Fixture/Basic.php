<?php

namespace Prezent\Tests\Fixture;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\TranslatableInterface;
use Prezent\Doctrine\Translatable\TranslationInterface;

/**
 * @ORM\Entity
 */
class Basic implements TranslatableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @Prezent\CurrentLocale
     */
    public $currentLocale;

    /**
     * @Prezent\FallbackLocale
     */
    public $fallbackLocale;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Prezent\Tests\Fixture\BasicTranslation",
     *     mappedBy="translatable",
     *     cascade={"persist", "remove", "merge"},
     *     orphanRemoval=true,
     *     indexBy="locale",
     *     fetch="EXTRA_LAZY"
     * )
     * @Prezent\Translations(targetEntity="Prezent\Tests\Fixture\BasicTranslation")
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getTranslations()
    {
        return $this->translations;
    }
    
    public function addTranslation(TranslationInterface $translation)
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setTranslatable($this);
        }
    
        return $this;
    }
    
    public function removeTranslation(TranslationInterface $translation)
    {
        if ($this->translations->removeElement($translation)) {
            $translation->setTranslatable(null);
        }
    
        return $this;
    }
}
