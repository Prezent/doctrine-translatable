<?php

namespace Prezent\Tests\Doctrine\Translatable\Mapping;

use Prezent\Tests\Tool\ORMTestCase;

class ClassMetadataFactoryTest extends ORMTestCase
{
    public function testClassMetadata()
    {
        $classMetadata = $this->getTranslatableListener()->getMetadataFactory()->getMetadataForClass('Prezent\\Tests\\Fixture\\Entry');

        $this->assertEquals('Prezent\\Tests\\Fixture\\EntryTranslation', $classMetadata->translationEntityClass);
        $this->assertInstanceOf('Prezent\\Doctrine\\Translatable\\Mapping\\PropertyMetadata', $classMetadata->currentTranslationProperty);
        $this->assertEquals('currentTranslation', $classMetadata->currentTranslationProperty->name);
        $this->assertInstanceOf('Prezent\\Doctrine\\Translatable\\Mapping\\PropertyMetadata', $classMetadata->fallbackTranslationProperty);
        $this->assertEquals('currentTranslation', $classMetadata->fallbackTranslationProperty->name);
        $this->assertInstanceOf('Prezent\\Doctrine\\Translatable\\Mapping\\PropertyMetadata', $classMetadata->translationsProperty);
        $this->assertEquals('translations', $classMetadata->translationsProperty->name);
    }
}
