<?php

namespace Prezent\Tests\Fixture;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;

/**
 * @ORM\Entity
 */
class EntryTranslation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Prezent\Tests\Fixture\Entry", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $object;

    /**
     * @ORM\Column(name="locale", type="string")
     */
    private $locale;

    /**
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    public function getObject()
    {
        return $this->object;
    }
    
    public function setObject(Entry $object = null)
    {
        if ($this->object == $object) {
            return $this;
        }
    
        $old = $this->object;
        $this->object = $object;
    
        if ($old !== null) {
            $old->removeTranslation($this);
        }
    
        if ($object !== null) {
            $object->addTranslation($this);
        }
    
        return $this;
    }

    /**
     * Getter for locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
    
    /**
     * Setter for locale
     *
     * @param string $locale
     * @return self
     */
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
