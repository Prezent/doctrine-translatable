<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\Mapping\Driver;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\Driver\FileLocator;
use Doctrine\Common\Persistence\ObjectManager;
use Metadata\Driver\DriverChain as MetadataDriverChain;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Metadata\Driver\DriverInterface;
use Doctrine\Common\Persistence\Mapping\Driver\FileDriver as DoctrineFileDriver;
use Prezent\Doctrine\Translatable\Mapping\TranslatableMetadata;
use Prezent\Doctrine\Translatable\Mapping\TranslationMetadata;

/**
 * FileDriver provides the base methods to read mapping information from a file.
 *
 * @author Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
abstract class FileDriver implements DriverInterface
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Only one of $managerRegistry or $objectManager must be given.
     *
     * @param ObjectManager $objectManager
     * @param ManagerRegistry $managerRegistry
     * @throws \InvalidArgumentException
     */
    public function __construct(ObjectManager $objectManager = null, ManagerRegistry $managerRegistry = null)
    {
        if (null === $objectManager && null === $managerRegistry) {
            throw new \InvalidArgumentException('One of $managerRegistry or $objectManager must be set');
        } elseif (null !== $objectManager && null !== $managerRegistry) {
            throw new \InvalidArgumentException('One of $managerRegistry or $objectManager must be set, not both');
        }

        $this->managerRegistry = $managerRegistry;
        $this->objectManager = $objectManager;
    }

    /**
     * @param \ReflectionClass $class
     * @return \Metadata\ClassMetadata
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if ($class->implementsInterface('Prezent\\Doctrine\\Translatable\\TranslatableInterface')) {
            return $this->loadTranslatableMetadata($class->name, $this->readMapping($class->name));
        }

        if ($class->implementsInterface('Prezent\\Doctrine\\Translatable\\TranslationInterface')) {
            return $this->loadTranslationMetadata($class->name, $this->readMapping($class->name));
        }
    }

    /**
     * Returns the mapping filename for the given classname.
     *
     * @param string $className
     * @return string|null
     */
    protected function getMappingFile($className)
    {
        $om = $this->objectManager ?: $this->managerRegistry->getManagerForClass($className);

        if (! $om) {
            return null;
        }

        $locator = $this->getLocator($om->getConfiguration()->getMetadataDriverImpl());

        if (! $locator) {
            return null;
        }

        try {
            return $locator->findMappingFile($className);
        } catch (MappingException $e) {
        }

        return null;
    }


    /**
     * Load metadata for a translatable class
     *
     * @param string $className
     * @param mixed $config
     * @return TranslatableMetadata|null
     */
    abstract protected function loadTranslatableMetadata($className, $config);

    /**
     * Load metadata for a translation class
     *
     * @param string $className
     * @param mixed $config
     * @return TranslationMetadata|null
     */
    abstract protected function loadTranslationMetadata($className, $config);

    /**
     * Parses the given mapping file.
     * @param string $file
     * @return mixed
     */
    abstract protected function parse($file);

    /**
     * Returns whether the given doctrine file driver is valid for this type of file.
     *
     * @param DoctrineFileDriver $driver
     * @return bool
     */
    abstract protected function isValidDriver(DoctrineFileDriver $driver);

    /**
     * @param mixed $omDriver
     * @return FileLocator|null
     */
    private function getLocator($omDriver)
    {
        if ($omDriver instanceof MetadataDriverChain || $omDriver instanceof MappingDriverChain) {

            $locators = array();

            foreach ($omDriver->getDrivers() as $nestedOmDriver) {
                $locator = $this->getLocator($nestedOmDriver);

                if ($locator) {
                    $locators[] = $locator;
                }
            }

            return $locators ? new FileLocatorChain($locators) : null;

        } else if ($omDriver instanceof DoctrineFileDriver && $this->isValidDriver($omDriver)) {
            return $omDriver->getLocator();
        }

        return null;
    }


    /**
     * Reads the configuration for the given classname.
     *
     * @param string $className
     * @return mixed|null
     */
    private function readMapping($className)
    {
        $file = $this->getMappingFile($className);

        return $file ? $this->parse($file) : null;
    }
}
