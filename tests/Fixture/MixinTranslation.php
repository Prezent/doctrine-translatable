<?php

namespace Prezent\Tests\Fixture;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Attribute as Prezent;
use Prezent\Doctrine\Translatable\TranslationInterface;
use Prezent\Doctrine\Translatable\Entity\TranslationTrait;

#[ORM\Entity]
class MixinTranslation implements TranslationInterface
{
    use TranslationTrait;

    #[Prezent\Translatable(targetEntity: Mixin::class)]
    protected $translatable;

    #[ORM\Column(name: 'name', type: 'string')]
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
