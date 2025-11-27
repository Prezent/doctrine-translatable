<?php

namespace Prezent\Tests\Fixture;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Attribute as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

#[ORM\Entity]
class BadMapping extends AbstractTranslatable
{
    // Intentionally invalid: Translations attribute without a targetEntity
    #[Prezent\Translations(targetEntity: '')]
    protected $translations;
}
