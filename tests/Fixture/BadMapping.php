<?php

namespace Prezent\Tests\Fixture;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

/**
 * @ORM\Entity
 */
class BadMapping extends AbstractTranslatable
{
    /**
     * @Prezent\Translations
     */
    protected $translations;
}
