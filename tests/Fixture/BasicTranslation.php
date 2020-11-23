<?php

namespace Prezent\Tests\Fixture;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\TranslatableInterface;
use Prezent\Doctrine\Translatable\TranslationInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"translatable_id", "locale"})}
 * )
 */
class BasicTranslation implements TranslationInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Prezent\Tests\Fixture\Basic", inversedBy="translations")
     * @ORM\JoinColumn(name="translatable_id", referencedColumnName="id", onDelete="CASCADE")
     * @Prezent\Translatable(targetEntity="Prezent\Tests\Fixture\Basic")
     */
    private $translatable;

    /**
     * @ORM\Column(name="locale", type="string")
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
    
    public function setTranslatable(TranslatableInterface $translatable = null)
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
