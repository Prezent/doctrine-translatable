<?php

namespace Prezent\Tests\Doctrine\Translatable\Mapping;

use Doctrine\Common\Persistence\Mapping\RuntimeReflectionService;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Prezent\Tests\Tool\ORMTestCase;

class TranslatableListenerValidationTest extends ORMTestCase
{
    /**
     * @expectedException Doctrine\Common\Annotations\AnnotationException
     */
    public function testAnnotationValidation()
    {
        $classMetadata = new ClassMetadata('Prezent\Tests\Fixture\BadMapping');
        $classMetadata->initializeReflection(new RuntimeReflectionService());

        $eventArgs = new LoadClassMetadataEventArgs($classMetadata, $this->getEntityManager());

        $this->getTranslatableListener()->loadClassMetadata($eventArgs);
    }

    /**
     * @expectedException Prezent\Doctrine\Translatable\Mapping\MappingException
     */
    public function testClassMetadataValidation()
    {
        $classMetadata = new ClassMetadata('Prezent\Tests\Fixture\BadMappingTranslation');
        $classMetadata->initializeReflection(new RuntimeReflectionService());

        $eventArgs = new LoadClassMetadataEventArgs($classMetadata, $this->getEntityManager());

        $this->getTranslatableListener()->loadClassMetadata($eventArgs);
    }
}
