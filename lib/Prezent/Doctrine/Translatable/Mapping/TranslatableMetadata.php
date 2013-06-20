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
    public $currentLocale;

    /**
     * @var PropertyMetadata
     */
    public $fallbackLocale;

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

        if ($object->currentLocale) {
            $this->currentLocale = $object->currentLocale;
        }

        if ($object->fallbackLocale) {
            $this->fallbackLocale = $object->fallbackLocale;
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
            $this->currentLocale  ? $this->currentLocale->name  : null,
            $this->fallbackLocale ? $this->fallbackLocale->name : null,
            $this->translations   ? $this->translations->name        : null,
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
            $currentLocale,
            $fallbackLocale,
            $translations,
            $parent
        ) = unserialize($str);

        parent::unserialize($parent);

        if ($currentLocale) {
            $this->currentLocale = $this->propertyMetadata[$currentLocale];
        }
        if ($fallbackLocale) {
            $this->fallbackLocale = $this->propertyMetadata[$fallbackLocale];
        }
        if ($translations) {
            $this->translations = $this->propertyMetadata[$translations];
        }
    }
}
