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
use Prezent\Doctrine\Translatable\Mapping\ClassMetadata;
use Prezent\Doctrine\Translatable\Mapping\PropertyMetadata;

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
        $classMetadata = new ClassMetadata($class->name);
        $propertiesMetadata = $propertiesAnnotations = array();

        foreach ($class->getProperties() as $property) {
            if ($property->class !== $class->name) {
                continue;
            }

            $propertyMetadata = new PropertyMetadata($class->name, $property->getName());

            if ($this->reader->getPropertyAnnotation($property, 'Prezent\\Doctrine\\Translatable\\Annotation\\CurrentTranslation')) {
                $classMetadata->currentTranslationProperty = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }
            
            if ($this->reader->getPropertyAnnotation($property, 'Prezent\\Doctrine\\Translatable\\Annotation\\FallbackTranslation')) {
                $classMetadata->fallbackTranslationProperty = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }
            
            if ($this->reader->getPropertyAnnotation($property, 'Prezent\\Doctrine\\Translatable\\Annotation\\Translations')) {
                if (!($oneToMany = $this->reader->getPropertyAnnotation($property, 'Doctrine\\ORM\\Mapping\\OneToMany'))) {
                    throw new \UnexpectedValueException('The Translations annotation can only be set on a oneToMany relationship');
                }

                $classMetadata->translationsProperty = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
                $classMetadata->translationEntityClass = $oneToMany->targetEntity;
            }

        }

        return $classMetadata;
    }
}
