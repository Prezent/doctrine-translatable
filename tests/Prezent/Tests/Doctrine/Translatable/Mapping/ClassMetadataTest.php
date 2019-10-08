<?php

namespace Prezent\Tests\Doctrine\Translatable\Mapping;

use PHPUnit\Framework\TestCase;
use Prezent\Doctrine\Translatable\Mapping\MappingException;
use Prezent\Doctrine\Translatable\Mapping\PropertyMetadata;
use Prezent\Doctrine\Translatable\Mapping\TranslatableMetadata;
use Prezent\Doctrine\Translatable\Mapping\TranslationMetadata;
use Prezent\Tests\Tool\ORMTestCase;

class ClassMetadataTest extends TestCase
{
    public function testTranslatableSerialization()
    {
        $currentProp = new PropertyMetadata('Prezent\\Tests\\Fixture\\Basic', 'currentLocale');
        $fallbackProp = new PropertyMetadata('Prezent\\Tests\\Fixture\\Basic', 'fallbackLocale');
        $transProp = new PropertyMetadata('Prezent\\Tests\\Fixture\\Basic', 'translations');

        $meta = new TranslatableMetadata('Prezent\\Tests\\Fixture\\Basic');
        $meta->targetEntity = 'Prezent\\Tests\\Fixture\\BasicTranslation';
        $meta->currentLocale = $currentProp;
        $meta->fallbackLocale = $fallbackProp;
        $meta->translations = $transProp;
        $meta->addPropertyMetadata($currentProp);
        $meta->addPropertyMetadata($fallbackProp);
        $meta->addPropertyMetadata($transProp);

        $string = serialize($meta);
        $copy = unserialize($string);

        $this->assertEquals($meta, $copy);
        $this->assertSame($meta->currentLocale, $meta->propertyMetadata['currentLocale']);
    }

    public function testTranslationSerialization()
    {
        $transProp = new PropertyMetadata('Prezent\\Tests\\Fixture\\BasicTranslation', 'translatable');
        $localeProp = new PropertyMetadata('Prezent\\Tests\\Fixture\\BasicTranslation', 'locale');

        $meta = new TranslationMetadata('Prezent\\Tests\\Fixture\\BasicTranslation');
        $meta->targetEntity = 'Prezent\\Tests\\Fixture\\Basic';
        $meta->translatable = $transProp;
        $meta->locale = $localeProp;
        $meta->addPropertyMetadata($transProp);
        $meta->addPropertyMetadata($localeProp);

        $string = serialize($meta);
        $copy = unserialize($string);

        $this->assertEquals($meta, $copy);
        $this->assertSame($meta->locale, $meta->propertyMetadata['locale']);
    }

    public function testTranslatableValidation()
    {
        $this->expectException(MappingException::class);

        $meta = new TranslatableMetadata('Prezent\\Tests\\Fixture\\Basic');
        $meta->validate();
    }

    public function testTranslationValidation()
    {
        $this->expectException(MappingException::class);

        $meta = new TranslationMetadata('Prezent\\Tests\\Fixture\\BasicTranslation');
        $meta->validate();
    }
}
