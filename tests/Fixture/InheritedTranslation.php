<?php

namespace Prezent\Tests\Fixture;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Attribute as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

#[ORM\Entity]
class InheritedTranslation extends AbstractTranslation
{
    #[Prezent\Translatable(targetEntity: Inherited::class)]
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
