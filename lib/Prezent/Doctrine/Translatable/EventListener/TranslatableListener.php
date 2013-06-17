<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\EventListener;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Metadata\Driver\DriverChain;
use Metadata\MetadataFactory;
use Prezent\Doctrine\Translatable\Mapping\TranslatableMetadata;
use Prezent\Doctrine\Translatable\Mapping\TranslationMetadata;
use Prezent\Doctrine\Translatable\TranslatableInterface;
use Prezent\Doctrine\Translatable\TranslationInterface;

/**
 * Load translations on demand
 *
 * @see EventSubscriber
 */
class TranslatableListener implements EventSubscriber
{
    /**
     * @var string Locale to load translations in
     */
    private $currentLocale = 'en';

    /**
     * @var string Locale to use when the current locale is not available
     */
    private $fallbackLocale = 'en';

    /**
     * @var bool
     */
    private $fallbackMode = false;

    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * @var array
     */
    private $queries = array();

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
     * Get the current fallback mode
     *
     * @return bool True if fallback is enabled, false otherwise
     */
    public function getFallbackMode()
    {
        return $this->fallbackMode;
    }
    
    /**
     * Enable/disable fallback mode
     *
     * @param bool $fallbackMode
     * @return self
     */
    public function setFallbackMode($fallbackMode = true)
    {
        $this->fallbackMode = $fallbackMode;
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
    public function getSubscribedEvents()
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

        if (!$reflClass || $reflClass->isAbstract()) {
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
        $metadata = $this->metadataFactory->getMetadataForClass($mapping->name);
        if (!$mapping->hasAssociation($metadata->translations->name)) {
            $targetMetadata = $this->metadataFactory->getMetadataForClass($metadata->targetEntity);

            $mapping->mapOneToMany(array(
                'fieldName'     => $metadata->translations->name,
                'targetEntity'  => $metadata->targetEntity,
                'mappedBy'      => $targetMetadata->translatable->name,
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
        $metadata = $this->metadataFactory->getMetadataForClass($mapping->name);

        // Map translatable relation
        if (!$mapping->hasAssociation($metadata->translatable->name)) {
            $targetMetadata = $this->metadataFactory->getMetadataForClass($metadata->targetEntity);

            $mapping->mapManyToOne(array(
                'fieldName'    => $metadata->translatable->name,
                'targetEntity' => $metadata->targetEntity,
                'inversedBy'   => $targetMetadata->translations->name,
                'joinColumns'  => array(array(
                    'name'                 => 'translatable_id',
                    'referencedColumnName' => 'id',
                    'onDelete'             => 'CASCADE',
                )),
            ));
        }

        // Map locale field
        if (!$mapping->hasField($metadata->locale->name)) {
            $mapping->mapField(array(
                'fieldName' => $metadata->locale->name,
                'type' => 'string',
            ));
        }

        // Map unique index
        $columns = array(
            $mapping->getSingleAssociationJoinColumnName($metadata->translatable->name),
            $metadata->locale->name,
        );

        if (!$this->hasUniqueConstraint($mapping, $columns)) {
            $constraints = isset($mapping->table['uniqueConstraints']) ? $mapping->table['UniqueConstraints']: array();
            $constraints[$mapping->getTableName() . '_uniq_trans'] = array(
                'columns' => $columns,
            );

            $mapping->setPrimaryTable(array(
                'uniqueConstraints' => $constraints,
            ));
        }
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
        $metadata = $this->metadataFactory->getMetadataForClass(get_class($entity));

        if ($metadata instanceof TranslatableMetadata) {

            $locale = $this->fallbackMode
                ? array($this->currentLocale, $this->fallbackLocale)
                : $this->currentLocale;

            $translations = $this->getQuery($args->getEntityManager(), $metadata)
                ->setParameter('translatable', $entity)
                ->setParameter('locale', $locale, is_array($locale) ? Connection::PARAM_STR_ARRAY : null)
                ->getResult();

            // Set current translation
            if (isset($translations[$this->currentLocale])) {
                $metadata->currentTranslation->setValue($entity, $translations[$this->currentLocale]);
            }

            // Set fallback translation
            if ($this->fallbackMode && isset($translations[$this->fallbackLocale]) && !$metadata->fallbackTranslation->getValue($entity)) {
                $metadata->fallbackTranslation->setValue($entity, $translations[$this->fallbackLocale]);
            }
        }
    }

    /**
     * Get the translations query
     *
     * @param EntityManager $em
     * @param TranslatableMetadata $metadata
     * @return Query
     */
    private function getQuery(EntityManager $em, TranslatableMetadata $metadata)
    {
        $hash = $this->getQueryHash($metadata);

        if (!isset($this->queries[$hash])) {
            $qb = $em->createQueryBuilder();
            $qb->select('t')
                ->from($metadata->targetEntity, 't', 't.locale')
                ->where('t.translatable = :translatable');

            if ($this->fallbackMode) {
                $qb->andWhere('t.locale IN (:locale)');
            } else {
                $qb->andWhere('t.locale = :locale');
            }

            $this->queries[$hash] = $qb->getQuery();
        }

        return $this->queries[$hash];
    }

    /**
     * Hash a query ID
     *
     * @param TranslatableMetadata $metadata
     * @return string
     */
    private function getQueryHash($metadata)
    {
        return md5((int) $this->fallbackMode . ':' . $metadata->name);
    }
}
