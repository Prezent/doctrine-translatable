<?php

declare(strict_types=1);

namespace Prezent\Doctrine\Translatable\Mapping\Driver;

use Metadata\ClassMetadata;
use Metadata\Driver\DriverInterface;
use Prezent\Doctrine\Translatable\Mapping\PropertyMetadata;
use Prezent\Doctrine\Translatable\Mapping\TranslatableMetadata;
use Prezent\Doctrine\Translatable\Mapping\TranslationMetadata;

final class AttributeDriver implements DriverInterface
{
    /** @var array<string> */
    private array $paths;

    /**
     * @param array<string> $paths
     */
    public function __construct(array $paths = [])
    {
        $this->paths = $paths;
    }

    public function loadMetadataForClass(\ReflectionClass $class): ?ClassMetadata
    {
        if ($class->implementsInterface('Prezent\\Doctrine\\Translatable\\TranslatableInterface')) {
            return $this->loadTranslatableMetadata($class);
        }

        if ($class->implementsInterface('Prezent\\Doctrine\\Translatable\\TranslationInterface')) {
            return $this->loadTranslationMetadata($class);
        }

        return null;
    }

    private function loadTranslatableMetadata(\ReflectionClass $class): TranslatableMetadata
    {
        $classMetadata = new TranslatableMetadata($class->name);

        foreach ($class->getProperties() as $property) {
            if ($property->class !== $class->name) {
                continue;
            }

            $propertyMetadata = new PropertyMetadata($class->name, $property->getName());
            $targetEntityDefault = $class->name . 'Translation';

            // #[CurrentLocale]
            if ($this->hasAttribute($property, 'Prezent\\Doctrine\\Translatable\\Attribute\\CurrentLocale')) {
                $classMetadata->currentLocale = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }

            // #[FallbackLocale]
            if ($this->hasAttribute($property, 'Prezent\\Doctrine\\Translatable\\Attribute\\FallbackLocale')) {
                $classMetadata->fallbackLocale = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }

            // #[Translations(targetEntity: ...)]
            if ($annot = $this->getAttributeInstance($property, 'Prezent\\Doctrine\\Translatable\\Attribute\\Translations')) {
                $classMetadata->targetEntity = $annot->targetEntity ?? $targetEntityDefault;
                $classMetadata->translations = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }
        }

        return $classMetadata;
    }

    private function loadTranslationMetadata(\ReflectionClass $class): TranslationMetadata
    {
        $classMetadata = new TranslationMetadata($class->name);

        foreach ($class->getProperties() as $property) {
            if ($property->class !== $class->name) {
                continue;
            }

            $propertyMetadata = new PropertyMetadata($class->name, $property->getName());
            $targetEntityGuess = 'Translation' === substr($class->name, -11) ? substr($class->name, 0, -11) : null;

            // #[Translatable(targetEntity: ..., referencedColumnName: ...)]
            if ($annot = $this->getAttributeInstance($property, 'Prezent\\Doctrine\\Translatable\\Attribute\\Translatable')) {
                $classMetadata->targetEntity = $annot->targetEntity ?? $targetEntityGuess;
                $classMetadata->referencedColumnName = $annot->referencedColumnName;
                $classMetadata->translatable = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }

            // #[Locale]
            if ($this->hasAttribute($property, 'Prezent\\Doctrine\\Translatable\\Attribute\\Locale')) {
                $classMetadata->locale = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }
        }

        return $classMetadata;
    }

    private function hasAttribute(\ReflectionProperty $prop, string $fqcn): bool
    {
        return [] !== $prop->getAttributes($fqcn);
    }

    /**
     * @template T
     * @param class-string<T> $fqcn
     * @return T|null
     */
    private function getAttributeInstance(\ReflectionProperty $prop, string $fqcn)
    {
        $attrs = $prop->getAttributes($fqcn);

        if (isset($attrs[0])) {
            return $attrs[0]->newInstance();
        }
        return null;
    }
}