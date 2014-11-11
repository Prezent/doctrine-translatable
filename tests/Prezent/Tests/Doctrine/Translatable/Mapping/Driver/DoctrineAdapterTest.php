<?php

namespace Prezent\Tests\Doctrine\Translatable\Mapping\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver as ORMAnnotationDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver as ORMYamlDriver;
use Metadata\Driver\DriverChain;
use Prezent\Doctrine\Translatable\Mapping\Driver\AnnotationDriver;
use Prezent\Doctrine\Translatable\Mapping\Driver\DoctrineAdapter;
use Prezent\Doctrine\Translatable\Mapping\Driver\YamlDriver;

class DoctrineAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testFromMetadataDriver()
    {
        $reader = new AnnotationReader();
        $locator = new SymfonyFileLocator(array(), '.yml');

        $omDriver = new MappingDriverChain();
        $omDriver->addDriver(new ORMAnnotationDriver($reader), 'Prezent\\Tests\\Fixture\\Foo');
        $omDriver->addDriver(new ORMYamlDriver($locator), 'Prezent\\Tests\\Fixture\\Bar');

        $expected = new DriverChain(array(
            new AnnotationDriver($reader),
            new YamlDriver($locator),
        ));

        $driver = DoctrineAdapter::fromMetadataDriver($omDriver);
        $this->assertEquals($expected, $driver);
    }
}
