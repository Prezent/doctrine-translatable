<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\Proxy;
use Metadata\MetadataFactory;
use Prezent\Doctrine\Translatable\Mapping\TranslatableMetadata;
use Prezent\Doctrine\Translatable\Mapping\TranslationMetadata;

/**
 * Load translations on demand
 *
 * @see EventSubscriber
 */
class TranslatableListener implements EventSubscriber
{
    /**
     * @var string Locale to use for translations
     */
    private $currentLocale = 'en';

    /**
     * @var string Locale to use when the current locale is not available
     */
    private $fallbackLocale = 'en';

    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * @var array
     */
    private $cache = array();

    /**
     * Constructor
     *
     * @param MetadataFactory $factory
     */
    public function __construct(MetadataFactory $factory)
    {
        $this->metadataFactory = $factory;
    }

    /**
     * Get the current locale
     *
     * @return string
     */
    public function getCurrentLocale()
    {
        return $this->currentLocale;
    }

    /**
     * Set the current locale
     *
     * @param string $currentLocale
     * @return self
     */
    public function setCurrentLocale($currentLocale)
    {
        $this->currentLocale = $currentLocale;
        return $this;
    }

    /**
     * Get the fallback locale
     *
     * @return string
     */
    public function getFallbackLocale()
    {
        return $this->fallbackLocale;
    }

    /**
     * Set the fallback locale
     *
     * @param string $fallbackLocale
     * @return self
     */
    public function setFallbackLocale($fallbackLocale)
    {
        $this->fallbackLocale = $fallbackLocale;
        return $this;
    }

    /**
     * Getter for metadataFactory
     *
     * @return MetadataFactory
     */
    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return array(
            Events::loadClassMetadata,
            Events::postLoad,
        );
    }

    /**
     * Add mapping to translatable entities
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     * @return void
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $reflClass = $classMetadata->reflClass;

        if (!$reflClass) {
            return;
        }

        if ($reflClass->implementsInterface('Prezent\Doctrine\Translatable\TranslatableInterface')) {
            $this->mapTranslatable($classMetadata);
        }

        if ($reflClass->implementsInterface('Prezent\Doctrine\Translatable\TranslationInterface')) {
            $this->mapTranslation($classMetadata);
        }
    }

    /**
     * Add mapping data to a translatable entity
     *
     * @param ClassMetadata $mapping
     * @return void
     */
    private function mapTranslatable(ClassMetadata $mapping)
    {
        $metadata = $this->getTranslatableMetadata($mapping->name);

        if ($metadata->targetEntity
            && $metadata->translations
            && !$mapping->hasAssociation($metadata->translations->name)
        ) {
            $targetMetadata = $this->getTranslatableMetadata($metadata->targetEntity);

            $mapping->mapOneToMany(array(
                'fieldName'     => $metadata->translations->name,
                'targetEntity'  => $metadata->targetEntity,
                'mappedBy'      => $targetMetadata->translatable->name,
                'fetch'         => ClassMetadataInfo::FETCH_EXTRA_LAZY,
                'indexBy'       => $targetMetadata->locale->name,
                'cascade'       => array('persist', 'merge', 'remove'),
                'orphanRemoval' => true,
            ));
        }
    }

    /**
     * Add mapping data to a translation entity
     *
     * @param ClassMetadata $mapping
     * @return void
     */
    private function mapTranslation(ClassMetadata $mapping)
    {
        $metadata = $this->getTranslatableMetadata($mapping->name);

        // Map translatable relation
        if ($metadata->targetEntity
            && $metadata->translatable
            && !$mapping->hasAssociation($metadata->translatable->name)
        ) {
            $targetMetadata = $this->getTranslatableMetadata($metadata->targetEntity);

            $mapping->mapManyToOne(array(
                'fieldName'    => $metadata->translatable->name,
                'targetEntity' => $metadata->targetEntity,
                'inversedBy'   => $targetMetadata->translations->name,
                'joinColumns'  => array(array(
                    'name'                 => 'translatable_id',
                    'referencedColumnName' => $metadata->referencedColumnName,
                    'onDelete'             => 'CASCADE',
                    'nullable'             => false,
                )),
            ));
        }

        if (!$metadata->translatable) {
            return;
        }

        // Map locale field
        if (!$mapping->hasField($metadata->locale->name)) {
            $mapping->mapField(array(
                'fieldName' => $metadata->locale->name,
                'type' => 'string',
                'length' => 5,
            ));
        }

        // Map unique index
        $columns = array(
            $mapping->getSingleAssociationJoinColumnName($metadata->translatable->name),
            $metadata->locale->name,
        );

        if (!$this->hasUniqueConstraint($mapping, $columns)) {
            $constraints = isset($mapping->table['uniqueConstraints']) ? $mapping->table['uniqueConstraints']: array();
            $constraints[$mapping->getTableName() . '_uniq_trans'] = array(
                'columns' => $columns,
            );

            $mapping->setPrimaryTable(array(
                'uniqueConstraints' => $constraints,
            ));
        }
    }

    /**
     * Get translatable metadata
     *
     * @param string $className
     * @return TranslatableMetadata|TranslationMetadata
     */
    public function getTranslatableMetadata($className)
    {
        if (array_key_exists($className, $this->cache)) {
            return $this->cache[$className];
        }

        if ($metadata = $this->metadataFactory->getMetadataForClass($className)) {
            $reflection = new \ReflectionClass($className);

            if (!$reflection->isAbstract()) {
                $metadata->validate();
            }
        }

        $this->cache[$className] = $metadata;
        return $metadata;
    }

    /**
     * Check if an unique constraint has been defined
     *
     * @param ClassMetadata $mapping
     * @param array $columns
     * @return bool
     */
    private function hasUniqueConstraint(ClassMetadata $mapping, array $columns)
    {
        if (!array_diff($mapping->getIdentifierColumnNames(), $columns)) {
            return true;
        }

        if (!isset($mapping->table['uniqueConstraints'])) {
            return false;
        }

        foreach ($mapping->table['uniqueConstraints'] as $constraint) {
            if (!array_diff($constraint['columns'], $columns)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load translations
     *
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $class = $args->getEntityManager()->getClassMetadata(get_class($entity))->getName(); // Resolve proxy class
        $metadata = $this->getTranslatableMetadata($class);

        if ($metadata instanceof TranslatableMetadata) {
            if ($metadata->fallbackLocale) {
                $this->setReflectionPropertyValue($entity, $class, 'fallbackLocale', $this->getFallbackLocale());
            }

            if ($metadata->currentLocale) {
                $this->setReflectionPropertyValue($entity, $class, 'currentLocale', $this->getCurrentLocale());
            }
        }
    }

    /**
     * @param object $object
     * @param string $property
     * @param mixed $value
     */
    private function setReflectionPropertyValue($object, string $class, string $property, $value): void
    {
        // Cannot set property on Doctrine Proxy
        if ($object instanceof Proxy) {
            $object->__load();
        }

        $reflection = new \ReflectionProperty($class, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }
}
