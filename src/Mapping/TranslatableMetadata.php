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
    public function merge(MergeableInterface $object): void
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
     * @return array{targetEntity: string, currentLocale: ?string, fallbackLocale: ?string, translations: ?string, parent: mixed[]}
     */
    public function __serialize(): array
    {
        return [
                'targetEntity' => $this->targetEntity,
                'currentLocale' => $this->currentLocale->name ?? null,
                'fallbackLocale' => $this->fallbackLocale->name ?? null,
                'translations' => $this->translations->name ?? null,
                'parent' => $this->serializeToArray(),
            ];
    }

    /**
     * @param array{targetEntity: string, currentLocale: ?string, fallbackLocale: ?string, translations: ?string, parent: mixed[]} $data
     */
    public function __unserialize(array $data): void
    {
        $this->targetEntity = $data['targetEntity'];

        $this->unserializeFromArray($data['parent']);

        if ($data['currentLocale']) {
            $this->currentLocale = $this->propertyMetadata[$data['currentLocale']];
        }
        if ($data['fallbackLocale']) {
            $this->fallbackLocale = $this->propertyMetadata[$data['fallbackLocale']];
        }
        if ($data['translations']) {
            $this->translations = $this->propertyMetadata[$data['translations']];
        }
    }
}
