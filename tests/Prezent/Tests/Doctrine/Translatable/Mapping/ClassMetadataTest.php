<?php

namespace Prezent\Tests\Doctrine\Translatable\Mapping;

use Prezent\Doctrine\Translatable\Mapping\PropertyMetadata;
use Prezent\Doctrine\Translatable\Mapping\TranslatableMetadata;
use Prezent\Doctrine\Translatable\Mapping\TranslationMetadata;
use Prezent\Tests\Tool\ORMTestCase;

class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testTranslatableSerialization()
    {
        $currentProp = new PropertyMetadata('Prezent\\Tests\\Fixture\\Entry', 'currentTranslation');
        $transProp = new PropertyMetadata('Prezent\\Tests\\Fixture\\Entry', 'translations');

        $meta = new TranslatableMetadata('Prezent\\Tests\\Fixture\\Entry');
        $meta->targetEntity = 'Prezent\\Tests\\Fixture\\EntryTranslation';
        $meta->currentTranslation = $currentProp;
        $meta->fallbackTranslation = $currentProp;
        $meta->translations = $transProp;
        $meta->addPropertyMetadata($currentProp);
        $meta->addPropertyMetadata($transProp);

        $string = serialize($meta);
        $copy = unserialize($string);

        $this->assertEquals($meta, $copy);
        $this->assertSame($meta->currentTranslation, $meta->propertyMetadata['currentTranslation']);
    }

    public function testTranslationSerialization()
    {
        $transProp = new PropertyMetadata('Prezent\\Tests\\Fixture\\EntryTranslation', 'translatable');
        $localeProp = new PropertyMetadata('Prezent\\Tests\\Fixture\\EntryTranslation', 'locale');

        $meta = new TranslationMetadata('Prezent\\Tests\\Fixture\\EntryTranslation');
        $meta->targetEntity = 'Prezent\\Tests\\Fixture\\Entry';
        $meta->translatable = $transProp;
        $meta->locale = $localeProp;
        $meta->addPropertyMetadata($transProp);
        $meta->addPropertyMetadata($localeProp);

        $string = serialize($meta);
        $copy = unserialize($string);

        $this->assertEquals($meta, $copy);
        $this->assertSame($meta->locale, $meta->propertyMetadata['locale']);
    }
}
