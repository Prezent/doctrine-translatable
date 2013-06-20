<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\Mapping\Driver;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Metadata\Driver\DriverInterface;
use Prezent\Doctrine\Translatable\Annotation\CurrentTranslation;
use Prezent\Doctrine\Translatable\Annotation\FallbackTranslation;
use Prezent\Doctrine\Translatable\Annotation\Translations;
use Prezent\Doctrine\Translatable\Mapping\PropertyMetadata;
use Prezent\Doctrine\Translatable\Mapping\TranslatableMetadata;
use Prezent\Doctrine\Translatable\Mapping\TranslationMetadata;

/**
 * Load translation metadata from annotations
 */
class AnnotationDriver implements DriverInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * Constructor
     *
     * @param Reader $reader
     * @param ClassMetadataFactory $factory Doctrine's metadata factory
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if ($class->implementsInterface('Prezent\\Doctrine\\Translatable\\TranslatableInterface')) {
            return $this->loadTranslatableMetadata($class);
        }

        if ($class->implementsInterface('Prezent\\Doctrine\\Translatable\\TranslationInterface')) {
            return $this->loadTranslationMetadata($class);
        }
    }

    /**
     * Load metadata for a translatable class
     *
     * @param \ReflectionClass $class
     * @return TranslatableMetadata
     */
    private function loadTranslatableMetadata(\ReflectionClass $class) {
        $classMetadata = new TranslatableMetadata($class->name);

        foreach ($class->getProperties() as $property) {
            if ($property->class !== $class->name) {
                continue;
            }

            $propertyMetadata = new PropertyMetadata($class->name, $property->getName());

            if ($this->reader->getPropertyAnnotation($property, 'Prezent\\Doctrine\\Translatable\\Annotation\\CurrentLocale')) {
                $classMetadata->currentLocale = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }
            
            if ($this->reader->getPropertyAnnotation($property, 'Prezent\\Doctrine\\Translatable\\Annotation\\FallbackLocale')) {
                $classMetadata->fallbackLocale = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }
            
            if ($annot = $this->reader->getPropertyAnnotation($property, 'Prezent\\Doctrine\\Translatable\\Annotation\\Translations')) {
                $classMetadata->targetEntity = $annot->targetEntity;
                $classMetadata->translations = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }
        }

        return $classMetadata;
    }

    /**
     * Load metadata for a translation class
     *
     * @param \ReflectionClass $class
     * @return TranslationMetadata
     */
    private function loadTranslationMetadata(\ReflectionClass $class)
    {
        $classMetadata = new TranslationMetadata($class->name);

        foreach ($class->getProperties() as $property) {
            if ($property->class !== $class->name) {
                continue;
            }

            $propertyMetadata = new PropertyMetadata($class->name, $property->getName());

            if ($annot = $this->reader->getPropertyAnnotation($property, 'Prezent\\Doctrine\\Translatable\\Annotation\\Translatable')) {
                $classMetadata->targetEntity = $annot->targetEntity;
                $classMetadata->translatable = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }

            if ($this->reader->getPropertyAnnotation($property, 'Prezent\\Doctrine\\Translatable\\Annotation\\Locale')) {
                $classMetadata->locale = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }
        }

        return $classMetadata;
    }
}
