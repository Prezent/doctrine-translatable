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
     * @var string
     */
    public $referencedColumnName = 'id';

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

        if (!$this->referencedColumnName) {
            throw new MappingException(sprintf('No translatable referencedColumnName specified for %s', $this->name));
        }

        if (!$this->locale) {
            throw new MappingException(sprintf('No locale specified for %s', $this->name));
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

        if ($object->referencedColumnName) {
            $this->referencedColumnName = $object->referencedColumnName;
        }

        if ($object->translatable) {
            $this->translatable = $object->translatable;
        }

        if ($object->locale) {
            $this->locale = $object->locale;
        }
    }

    /**
     * @return array{targetEntity: string, referencedColumnName: string, translatable: ?string, locale: ?string, parent: mixed[]}
     */
    public function __serialize(): array
    {
        return [
            'targetEntity' => $this->targetEntity,
            'referencedColumnName' => $this->referencedColumnName,
            'translatable' => $this->translatable->name ?? null,
            'locale' => $this->locale->name ?? null,
            'parent' => $this->serializeToArray(),
        ];
    }

    /**
     * @param array{targetEntity: string, referencedColumnName: string, translatable: ?string, locale: ?string, parent: mixed[]} $data
     */
    public function __unserialize(array $data): void
    {
        $this->targetEntity = $data['targetEntity'];
        $this->referencedColumnName = $data['referencedColumnName'];

        $this->unserializeFromArray($data['parent']);

        if ($data['translatable']) {
            $this->translatable = $this->propertyMetadata[$data['translatable']];
        }
        if ($data['locale']) {
            $this->locale = $this->propertyMetadata[$data['locale']];
        }
    }
}
