<?php

namespace Prezent\Tests\Fixture;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Attribute as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

#[ORM\Entity]
class BadMappingTranslation extends AbstractTranslation
{
    // Intentionally invalid: missing Translatable attribute on translatable property
    protected $translatable;
}
