<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\Mapping;

use Metadata\MergeableClassMetadata;
use Metadata\MergeableInterface;

/**
 * Class metadata for translations
 *
 * @see MergeableClassMetadata
 */
class TranslationMetadata extends MergeableClassMetadata
{
    /**
     * @var string
     */
    public $targetEntity;

    /**
     * @var PropertyMetadata
     */
    public $translatable;

    /**
     * @var PropertyMetadata
     */
    public $locale;

    /**
     * Validate the metadata
     *
     * @return void
     */
    public function validate()
    {
        if (!$this->translatable) {
            throw new MappingException(sprintf('No translatable specified for %s', $this->name));
        }

        if (!$this->targetEntity) {
            throw new MappingException(sprintf('No translatable targetEntity specified for %s', $this->name));
        }

        if (!$this->locale) {
            throw new MappingException(sprintf('No locale specified for %s', $this->name));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function merge(MergeableInterface $object)
    {
        if (!$object instanceof self) {
            throw new \InvalidArgumentException(sprintf('$object must be an instance of %s.', __CLASS__));
        }

        parent::merge($object);

        if ($object->targetEntity) {
            $this->targetEntity = $object->targetEntity;
        }

        if ($object->translatable) {
            $this->translatable = $object->translatable;
        }

        if ($object->locale) {
            $this->locale = $object->locale;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->targetEntity,
            $this->translatable ? $this->translatable->name : null,
            $this->locale       ? $this->locale->name       : null,
            parent::serialize(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($str)
    {
        list (
            $this->targetEntity,
            $translatable,
            $locale,
            $parent
        ) = unserialize($str);

        parent::unserialize($parent);

        if ($translatable) {
            $this->translatable = $this->propertyMetadata[$translatable];
        }
        if ($locale) {
            $this->locale = $this->propertyMetadata[$locale];
        }
    }
}
