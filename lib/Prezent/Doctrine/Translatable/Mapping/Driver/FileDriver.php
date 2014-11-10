<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\Mapping\Driver;

use Doctrine\Common\Persistence\Mapping\Driver\FileLocator;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Metadata\Driver\DriverInterface;
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
     * Constructor
     *
     * @param FileLocator $locator
     */
    public function __construct(FileLocator $locator)
    {
        $this->locator = $locator;
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
        try {
            return $this->locator->findMappingFile($className);
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
