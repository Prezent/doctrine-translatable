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
 * Class metadata for translatable entities
 *
 * @see MergeableClassMetadata
 */
class TranslatableMetadata extends MergeableClassMetadata
{
    /**
     * @var string
     */
    public $targetEntity;

    /**
     * @var PropertyMetadata
     */
    public $currentTranslation;

    /**
     * @var PropertyMetadata
     */
    public $fallbackTranslation;

    /**
     * @var PropertyMetadata
     */
    public $translations;

    /**
     * Validate the metadata
     *
     * @return void
     */
    public function validate()
    {
        if (!$this->translations) {
            throw new MappingException(sprintf('No translations specified for %s', $this->name));
        }

        if (!$this->targetEntity) {
            throw new MappingException(sprintf('No translations targetEntity specified for %s', $this->name));
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

        if ($object->currentTranslation) {
            $this->currentTranslation = $object->currentTranslation;
        }

        if ($object->fallbackTranslation) {
            $this->fallbackTranslation = $object->fallbackTranslation;
        }

        if ($object->translations) {
            $this->translations = $object->translations;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->targetEntity,
            $this->currentTranslation  ? $this->currentTranslation->name  : null,
            $this->fallbackTranslation ? $this->fallbackTranslation->name : null,
            $this->translations        ? $this->translations->name        : null,
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
            $currentTranslation,
            $fallbackTranslation,
            $translations,
            $parent
        ) = unserialize($str);

        parent::unserialize($parent);

        if ($currentTranslation) {
            $this->currentTranslation = $this->propertyMetadata[$currentTranslation];
        }
        if ($fallbackTranslation) {
            $this->fallbackTranslation = $this->propertyMetadata[$fallbackTranslation];
        }
        if ($translations) {
            $this->translations = $this->propertyMetadata[$translations];
        }
    }
}
