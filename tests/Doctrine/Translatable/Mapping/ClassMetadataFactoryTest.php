<?php

namespace Prezent\Tests\Doctrine\Translatable\Mapping;

use Prezent\Tests\Tool\ORMTestCase;

class ClassMetadataFactoryTest extends ORMTestCase
{
    public function testTranslatableMetadata()
    {
        $classMetadata = $this->getTranslatableListener()->getMetadataFactory()->getMetadataForClass('Prezent\\Tests\\Fixture\\Basic');

        $this->assertEquals('Prezent\\Tests\\Fixture\\BasicTranslation', $classMetadata->targetEntity);
        $this->assertInstanceOf('Prezent\\Doctrine\\Translatable\\Mapping\\PropertyMetadata', $classMetadata->currentLocale);
        $this->assertEquals('currentLocale', $classMetadata->currentLocale->name);
        $this->assertInstanceOf('Prezent\\Doctrine\\Translatable\\Mapping\\PropertyMetadata', $classMetadata->fallbackLocale);
        $this->assertEquals('fallbackLocale', $classMetadata->fallbackLocale->name);
        $this->assertInstanceOf('Prezent\\Doctrine\\Translatable\\Mapping\\PropertyMetadata', $classMetadata->translations);
        $this->assertEquals('translations', $classMetadata->translations->name);
    }

    public function testTranslationMetadata()
    {
        $classMetadata = $this->getTranslatableListener()->getMetadataFactory()->getMetadataForClass('Prezent\\Tests\\Fixture\\BasicTranslation');

        $this->assertEquals('Prezent\\Tests\\Fixture\\Basic', $classMetadata->targetEntity);
        $this->assertInstanceOf('Prezent\\Doctrine\\Translatable\\Mapping\\PropertyMetadata', $classMetadata->translatable);
        $this->assertEquals('translatable', $classMetadata->translatable->name);
        $this->assertInstanceOf('Prezent\\Doctrine\\Translatable\\Mapping\\PropertyMetadata', $classMetadata->locale);
        $this->assertEquals('locale', $classMetadata->locale->name);
    }
}
