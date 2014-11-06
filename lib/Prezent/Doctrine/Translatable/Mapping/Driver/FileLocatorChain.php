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

/**
 * FileLocatorChain
 *
 * @author Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class FileLocatorChain implements FileLocator
{
    /**
     * @var array
     */
    private $locators = array();

    /**
     * @param array $locators optional array of file locators
     */
    public function __construct(array $locators = array())
    {
        foreach ($locators as $locator) {
            $this->addLocator($locator);
        }
    }

    /**
     * @param FileLocator $locator
     * @return $this
     */
    public function addLocator(FileLocator $locator)
    {
        $this->locators[] = $locator;

        return $this;
    }

    /**
     * @param FileLocator $locator
     * @return bool
     */
    public function removeLocator(FileLocator $locator)
    {
        $key = array_search($locator, $this->locators, true);

        if ($key !== false) {
            unset($this->locators[$key]);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function findMappingFile($className)
    {
        foreach ($this->locators as $locator) {
            /** @var FileLocator $locator */
            try {
                return $locator->findMappingFile($className);
            } catch (MappingException $e) {
            }
        }

        if (isset($e)) {
            throw $e;
        }

        throw MappingException::mappingFileNotFound($className, '');
    }

    /**
     * {@inheritdoc}
     */
    public function getAllClassNames($globalBasename)
    {
        $classNames = array();

        foreach ($this->locators as $locator) {
            /** @var FileLocator $locator */
            $classNames = array_merge($classNames, $locator->getAllClassNames($globalBasename));
        }

        return array_unique($classNames);
    }

    /**
     * {@inheritdoc}
     */
    public function fileExists($className)
    {
        foreach ($this->locators as $locator) {
            /** @var FileLocator $locator */
            if ($locator->fileExists($className)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaths()
    {
        $paths = array();

        foreach ($this->locators as $locator) {
            /** @var FileLocator $locator */
            $paths = array_merge($paths, $locator->getPaths());
        }

        return array_unique($paths);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileExtension()
    {
        if ($this->locators) {
            return $this->locators[0]->getFileExtension();
        }

        return null;
    }
}
