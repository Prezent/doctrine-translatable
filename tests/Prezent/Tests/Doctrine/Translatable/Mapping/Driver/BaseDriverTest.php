<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Tests\Doctrine\Translatable\Mapping\Driver;

abstract class BaseDriverTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadTranslatableMetadata()
    {
        $metadata = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('Prezent\\Tests\\Fixture\\Basic'));

        $this->assertEquals($metadata->targetEntity, 'Prezent\\Tests\\Fixture\\BasicTranslation');
        $this->assertEquals($metadata->currentLocale->name, 'currentLocale');
        $this->assertEquals($metadata->fallbackLocale->name, 'fallbackLocale');
        $this->assertEquals($metadata->translations->name, 'translations');
    }

    public function testLoadTranslationMetadata()
    {
        $metadata = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('Prezent\\Tests\\Fixture\\BasicTranslation'));

        $this->assertEquals($metadata->targetEntity, 'Prezent\\Tests\\Fixture\\Basic');
        $this->assertEquals($metadata->translatable->name, 'translatable');
        $this->assertEquals($metadata->locale->name, 'locale');
    }

    abstract protected function getDriver();
}
