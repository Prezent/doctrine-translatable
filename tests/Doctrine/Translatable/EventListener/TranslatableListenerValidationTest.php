<?php

namespace Prezent\Tests\Doctrine\Translatable\EventListener;

use Doctrine\Persistence\Mapping\RuntimeReflectionService;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Prezent\Doctrine\Translatable\Mapping\MappingException;
use Prezent\Tests\Tool\ORMTestCase;

class TranslatableListenerValidationTest extends ORMTestCase
{
    public function testAnnotationValidation()
    {
        $this->expectException(MappingException::class);

        $classMetadata = new ClassMetadata('Prezent\Tests\Fixture\BadMapping');
        $classMetadata->initializeReflection(new RuntimeReflectionService());

        $eventArgs = new LoadClassMetadataEventArgs($classMetadata, $this->getEntityManager());

        $this->getTranslatableListener()->loadClassMetadata($eventArgs);
    }

    public function testClassMetadataValidation()
    {
        $this->expectException(MappingException::class);

        $classMetadata = new ClassMetadata('Prezent\Tests\Fixture\BadMappingTranslation');
        $classMetadata->initializeReflection(new RuntimeReflectionService());

        $eventArgs = new LoadClassMetadataEventArgs($classMetadata, $this->getEntityManager());

        $this->getTranslatableListener()->loadClassMetadata($eventArgs);
    }
}
