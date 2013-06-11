<?php

namespace Prezent\Tests\Doctrine\Translatable\Mapping;

use Prezent\Doctrine\Translatable\Mapping\ClassMetadata;
use Prezent\Doctrine\Translatable\Mapping\PropertyMetadata;
use Prezent\Tests\Tool\ORMTestCase;

class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialization()
    {
        $currentProp = new PropertyMetadata('Prezent\\Tests\\Fixture\\Entry', 'currentTranslation');
        $transProp = new PropertyMetadata('Prezent\\Tests\\Fixture\\Entry', 'translations');

        $meta = new ClassMetadata('Prezent\\Tests\\Fixture\\Entry');
        $meta->translationEntityClass = 'Prezent\\Tests\\Fixture\\EntryTranslation';
        $meta->currentTranslationProperty = $currentProp;
        $meta->fallbackTranslationProperty = $currentProp;
        $meta->translationsProperty = $transProp;
        $meta->addPropertyMetadata($currentProp);
        $meta->addPropertyMetadata($transProp);

        $string = serialize($meta);
        $this->assertEquals($meta, unserialize($string));
    }
}
