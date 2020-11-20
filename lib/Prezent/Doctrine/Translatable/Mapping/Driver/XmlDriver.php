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
 * @author Maarten de Boer <maarten@cloudstek.nl>
 */
class XmlDriver extends FileDriver
{
    /**
     * @inheritDoc
     */
    protected function loadTranslatableMetadata($className, $config)
    {
        if ($config === null || !$config instanceof SimpleXMLElement) {
            return null;
        }

        $config->registerXPathNamespace('prezent', 'https://prezent.nl/schemas/doctrine-translatable');
        $nodeList = $config->xpath('//prezent:translatable');

        if (count($nodeList) === 0) {
            return null;
        }

        if (count($nodeList) > 1) {
            throw new \Exception("Configuration defined twice");
        }

        $node = $nodeList[0];

        // Get class metadata
        $classMetadata = new TranslatableMetadata($className);

        // Target entity
        $translatableTargetEntity = (string)$node['target-entity'];
        $classMetadata->targetEntity = !empty($translatableTargetEntity)
            ? $translatableTargetEntity
            : $className . 'Translation';

        // Translations field
        $translationsField = (string)$node['translations'];

        $propertyMetadata = new PropertyMetadata(
            $className,
            !empty($translationsField) ? $translationsField : 'translations'
        );

        $classMetadata->translations = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        // Current locale
        $currentLocale = (string)$node['current-locale'];
        if (!empty($currentLocale)) {
            $propertyMetadata = new PropertyMetadata($className, $currentLocale);

            $classMetadata->currentLocale = $propertyMetadata;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        // Fallback locale``
        $fallbackLocale = (string)$node['fallback-locale'];
        if (!empty($fallbackLocale)) {
            $propertyMetadata = new PropertyMetadata($className, $fallbackLocale);

            $classMetadata->fallbackLocale = $propertyMetadata;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }

    /**
     * @inheritDoc
     */
    protected function loadTranslationMetadata($className, $config)
    {
        if ($config === null || !$config instanceof SimpleXMLElement) {
            return null;
        }

        $config->registerXPathNamespace('prezent', 'https://prezent.nl/schemas/doctrine-translatable');
        $nodeList = $config->xpath('//prezent:translation');

        if (count($nodeList) === 0) {
            return null;
        }

        if (count($nodeList) > 1) {
            throw new \Exception("Configuration defined twice");
        }

        $node = $nodeList[0];

        // Get class metadata
        $classMetadata = new TranslationMetadata($className);

        // Translatable field
        $translatableField = (string)$node['translatable'];
        $propertyMetadata = new PropertyMetadata(
            $className,
            !empty($translatableField) ? $translatableField : 'translatable'
        );

        $classMetadata->translatable = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        // Target entity
        $translatableTargetEntity = (string)$node['target-entity'];
        $classMetadata->targetEntity = !empty($translatableTargetEntity)
            ? $translatableTargetEntity
            : ('Translation' === substr($className, -11)
                ? substr($className, 0, -11)
                : null);

        // Referenced column name
        $translatableReferencedColumnName = (string)$node['referenced-column-name'] ?? 'id';
        $classMetadata->referencedColumnName = $translatableReferencedColumnName;

        // Locale
        $locale = (string)$node['locale'];

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
     * @inheritDoc
     */
    protected function parse($file)
    {
        return new SimpleXMLElement(file_get_contents($file));
    }
}
