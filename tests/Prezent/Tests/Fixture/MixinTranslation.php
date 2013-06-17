<?php

namespace Prezent\Tests\Fixture;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\TranslationInterface;
use Prezent\Doctrine\Translatable\Entity\TranslationTrait;

/**
 * @ORM\Entity
 */
class MixinTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @Prezent\Translatable(targetEntity="Prezent\Tests\Fixture\Mixin")
     */
    protected $translatable;

    /**
     * @ORM\Column(name="name", type="string")
     */
    private $name;

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
