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
        $currentProp = new PropertyMetadata('Prezent\\Tests\\Fixture\\Basic', 'currentTranslation');
        $transProp = new PropertyMetadata('Prezent\\Tests\\Fixture\\Basic', 'translations');

        $meta = new TranslatableMetadata('Prezent\\Tests\\Fixture\\Basic');
        $meta->targetEntity = 'Prezent\\Tests\\Fixture\\BasicTranslation';
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
}
