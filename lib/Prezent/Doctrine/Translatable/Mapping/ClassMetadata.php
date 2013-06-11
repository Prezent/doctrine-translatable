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
class ClassMetadata extends MergeableClassMetadata
{
    /**
     * @var string
     */
    public $translationEntityClass;

    /**
     * @var PropertyMetadata
     */
    public $currentTranslationProperty;

    /**
     * @var PropertyMetadata
     */
    public $fallbackTranslationProperty;

    /**
     * @var PropertyMetadata
     */
    public $translationsProperty;

    /**
     * Is this entity translatable
     *
     * @return bool
     */
    public function isTranslatable()
    {
        return (bool) $this->translationsProperty;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(MergeableInterface $object)
    {
        if (!$object instanceof ClassMetadata) {
            throw new \InvalidArgumentException(sprintf('$object must be an instance of %s.', __CLASS__));
        }

        parent::merge($object);

        if ($object->currentTranslationProperty) {
            $this->currentTranslationProperty = $object->currentTranslationProperty;
        }

        if ($object->fallbackTranslationProperty) {
            $this->fallbackTranslationProperty = $object->fallbackTranslationProperty;
        }

        if ($object->translationsProperty) {
            $this->translationsProperty = $object->translationsProperty;
        }

        if ($object->translationEntityClass) {
            $this->translationEntityClass = $object->translationEntityClass;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->translationEntityClass,
            $this->currentTranslationProperty->name,
            $this->fallbackTranslationProperty->name,
            $this->translationsProperty->name,
            parent::serialize(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($str)
    {
        list (
            $this->translationEntityClass,
            $currentTranslation,
            $fallbackTranslation,
            $translations,
            $parent
        ) = unserialize($str);

        parent::unserialize($parent);

        $this->currentTranslationProperty = $this->propertyMetadata[$currentTranslation];
        $this->fallbackTranslationProperty = $this->propertyMetadata[$fallbackTranslation];
        $this->translationsProperty = $this->propertyMetadata[$translations];
    }
}
