<?php

namespace Prezent\Tests\Doctrine\Translatable\Mapping\Driver;

use Doctrine\ORM\Mapping\Driver\AttributeDriver as ORMAttributeDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Metadata\Driver\DriverChain;
use PHPUnit\Framework\TestCase;
use Prezent\Doctrine\Translatable\Mapping\Driver\AttributeDriver;
use Prezent\Doctrine\Translatable\Mapping\Driver\DoctrineAdapter;

class DoctrineAdapterTest extends TestCase
{
    public function testFromMetadataDriver()
    {
        $paths = array(__DIR__ . '/../../../../Fixture');

        $omDriver = new MappingDriverChain();
        $omDriver->addDriver(new ORMAttributeDriver($paths), 'Prezent\\Tests\\Fixture\\Foo');

        $expected = new DriverChain(array(
            new AttributeDriver($paths),
        ));

        $driver = DoctrineAdapter::fromMetadataDriver($omDriver);
        $this->assertEquals($expected, $driver);
    }
}
