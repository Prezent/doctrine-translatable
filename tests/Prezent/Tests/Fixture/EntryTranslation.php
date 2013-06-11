<?php

namespace Prezent\Tests\Fixture;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="entry_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="translation_unique", columns={"object_id", "locale"})},
 *     indexes={@ORM\Index(name="locale_idx", columns={"locale"})}
 * )
 */
class EntryTranslation extends AbstractTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Prezent\Tests\Fixture\Entry", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected $object;

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
