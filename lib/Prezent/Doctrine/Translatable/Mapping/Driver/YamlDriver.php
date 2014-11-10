<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\Mapping\Driver;

use Doctrine\Common\Persistence\Mapping\Driver\FileDriver as DoctrineFileDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver as ORMYamlDriver;
use Prezent\Doctrine\Translatable\Mapping\PropertyMetadata;
use Prezent\Doctrine\Translatable\Mapping\TranslatableMetadata;
use Prezent\Doctrine\Translatable\Mapping\TranslationMetadata;
use Symfony\Component\Yaml\Yaml;

/**
 * Loads translatable metadata from Yaml mapping files.
 *
 * @author Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class YamlDriver extends FileDriver
{
    /**
     * Load metadata for a translatable class
     *
     * @param string $className
     * @param mixed $config
     * @return TranslatableMetadata|null
     */
    protected function loadTranslatableMetadata($className, $config)
    {
        if (! isset($config[$className])
            || ! isset($config[$className]['prezent'])
            || ! array_key_exists('translatable', $config[$className]['prezent'])
        ) {
            return;
        }

        $classMetadata = new TranslatableMetadata($className);

        $translatable = $config[$className]['prezent']['translatable'] ?: array();

        $propertyMetadata = new PropertyMetadata(
            $className,
            // defaults to translatable
            isset($translatable['field']) ? $translatable['field'] : 'translations'
        );

        // default targetEntity
        $targetEntity = $className . 'Translation';

        $classMetadata->targetEntity = isset($translatable['targetEntity']) ? $translatable['targetEntity']: $targetEntity;
        $classMetadata->translations = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        if (isset($translatable['currentLocale'])) {
            $propertyMetadata = new PropertyMetadata($className, $translatable['currentLocale']);

            $classMetadata->currentLocale = $propertyMetadata;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        if (isset($translatable['fallbackLocale'])) {
            $propertyMetadata = new PropertyMetadata($className, $translatable['fallbackLocale']);

            $classMetadata->fallbackLocale = $propertyMetadata;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }

    /**
     * Load metadata for a translation class
     *
     * @param string $className
     * @param mixed $config
     * @return TranslationMetadata|null
     */
    protected function loadTranslationMetadata($className, $config)
    {
        if (! isset($config[$className])
            || ! isset($config[$className]['prezent'])
            || ! array_key_exists('translatable', $config[$className]['prezent'])
        ) {
            return;
        }

        $classMetadata = new TranslationMetadata($className);

        $translatable = $config[$className]['prezent']['translatable'] ?: array();

        $propertyMetadata = new PropertyMetadata(
            $className,
            // defaults to translatable
            isset($translatable['field']) ? $translatable['field'] : 'translatable'
        );

        $targetEntity = 'Translation' === substr($className, -11) ? substr($className, 0, -11) : null;

        $classMetadata->targetEntity = isset($translatable['targetEntity']) ? $translatable['targetEntity']: $targetEntity;
        $classMetadata->translatable = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        $locale = isset($translatable['locale']) ? $translatable['locale'] : 'locale';
        $propertyMetadata = new PropertyMetadata($className, $locale);
        $classMetadata->locale = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        return $classMetadata;
    }

    /**
     * Parses the given mapping file.
     * @param string $file
     * @return mixed
     */
    protected function parse($file)
    {
        return Yaml::parse($file);
    }
}
