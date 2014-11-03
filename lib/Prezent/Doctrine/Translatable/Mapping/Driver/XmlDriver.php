<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\Mapping\Driver;

use Doctrine\Common\Persistence\Mapping\Driver\FileDriver as DoctrineFileDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver as ORMXmlDriver;
use Prezent\Doctrine\Translatable\Mapping\PropertyMetadata;
use Prezent\Doctrine\Translatable\Mapping\TranslatableMetadata;
use Prezent\Doctrine\Translatable\Mapping\TranslationMetadata;
use SimpleXMLElement;


/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class XmlDriver extends FileDriver
{
    /**
     * Load metadata for a translatable class
     *
     * @param string $className
     * @param mixed  $config
     *
     * @throws \Exception
     * @return TranslatableMetadata|null
     */
    protected function loadTranslatableMetadata($className, $config)
    {
        if (!$config) {
            return;
        }

        $xml = new SimpleXMLElement($config);

        $xml->registerXPathNamespace('prezent', 'prezent');

        $nodeList = $xml->xpath('//prezent:translatable');
        if (0 == count($nodeList)) {
            return;
        }

        if (1 < count($nodeList)) {
            throw new \Exception("Configuration defined twice");
        }

        $node = $nodeList[0];

        $classMetadata = new TranslatableMetadata($className);

        $translatableField = (string)$node['field'];

        $propertyMetadata = new PropertyMetadata(
            $className,
            // defaults to translatable
            !empty($translatableField) ? $translatableField : 'translations'
        );

        // default targetEntity
        $targetEntity = $className . 'Translation';

        $translatableTargetEntity    = (string)$node['target-entity'];
        $classMetadata->targetEntity = !empty($translatableTargetEntity) ? $translatableTargetEntity : $targetEntity;
        $classMetadata->translations = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        $currentLocale = (string)$node['current-locale'];
        if (!empty($currentLocale)) {
            $propertyMetadata = new PropertyMetadata($className, $currentLocale);

            $classMetadata->currentLocale = $propertyMetadata;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        $fallbackLocale = (string)$node['fallback-locale'];
        if (!empty($fallbackLocale)) {
            $propertyMetadata = new PropertyMetadata($className, $fallbackLocale);

            $classMetadata->fallbackLocale = $propertyMetadata;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }

    /**
     * Load metadata for a translation class
     *
     * @param string $className
     * @param mixed  $config
     *
     * @throws \Exception
     * @return TranslationMetadata|null
     */
    protected function loadTranslationMetadata($className, $config)
    {
        if (!$config) {
            return;
        }

        $xml = new SimpleXMLElement($config);
        $xml->registerXPathNamespace('prezent', 'prezent');
        $nodeList = $xml->xpath('//prezent:translatable');

        if (0 == count($nodeList)) {
            return;
        }

        if (1 < count($nodeList)) {
            throw new \Exception("Configuration defined twice");
        }

        $nodeTranslatable = $nodeList[0];

        $translatableField = (string)$nodeTranslatable['field'];

        $translatableTargetEntity = (string)$nodeTranslatable['target-entity'];

        $locale = (string)(string)$nodeTranslatable['locale'];

        $classMetadata = new TranslationMetadata($className);

        $propertyMetadata = new PropertyMetadata(
            $className,
            // defaults to translatable
            !empty($translatableField) ? $translatableField : 'translatable'
        );

        $targetEntity = 'Translation' === substr($className, -11) ? substr($className, 0, -11) : null;

        $classMetadata->targetEntity = !empty($translatableTargetEntity) ? $translatableTargetEntity : $targetEntity;
        $classMetadata->translatable = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        if ($locale) {
            $propertyMetadata = new PropertyMetadata($className, $locale);

            $classMetadata->locale = $propertyMetadata;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        // Set to default if no locale property has been set
        if (!$classMetadata->locale) {
            $propertyMetadata = new PropertyMetadata($className, 'locale');

            $classMetadata->locale = $propertyMetadata;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }

    /**
     * Parses the given mapping file.
     *
     * @param string $file
     *
     * @return mixed
     */
    protected function parse($file)
    {
        return file_get_contents($file);
    }
}
