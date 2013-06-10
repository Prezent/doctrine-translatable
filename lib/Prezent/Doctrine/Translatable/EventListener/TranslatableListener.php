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
use Doctrine\ORM\Query;
use Metadata\Driver\DriverChain;
use Metadata\MetadataFactory;

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
     * @var Query
     */
    private $query;

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
            Events::postLoad,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $metadata = $this->metadataFactory->getMetadataForClass(get_class($entity));

        if ($metadata->isTranslatable()) {

            $locale = $this->fallbackMode
                ? array($this->currentLocale, $this->fallbackLocale)
                : $this->currentLocale;

            $translations = $this->getQuery($args->getEntityManager(), $metadata)
                ->setParameter('object', $entity)
                ->setParameter('locale', $locale, is_array($locale) ? Connection::PARAM_STR_ARRAY : null)
                ->getResult();

            // Set current translation
            if (isset($translations[$this->currentLocale])) {
                $metadata->currentTranslationProperty->setValue($entity, $translations[$this->currentLocale]);
            }

            // Set fallback translation
            if ($this->fallbackMode && isset($translations[$this->fallbackLocale]) && !$metadata->fallbackTranslationProperty->getValue($entity)) {
                $metadata->fallbackTranslationProperty->setValue($entity, $translations[$this->fallbackLocale]);
            }
        }
    }

    /**
     * Get the translations query
     *
     * @param EntityManager $em
     * @return Query
     */
    private function getQuery(EntityManager $em, $metadata)
    {
        if (!$this->query) {
            $qb = $em->createQueryBuilder();
            $qb->select('t')
                ->from($metadata->translationEntityClass, 't', 't.locale')
                ->where('t.object = :object');

            if ($this->fallbackMode) {
                $qb->andWhere('t.locale IN (:locale)');
            } else {
                $qb->andWhere('t.locale = :locale');
            }

            $this->query = $qb->getQuery();
        }

        return $this->query;
    }
}
