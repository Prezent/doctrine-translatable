<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\Mapping\Driver;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver as DoctrineAnnotationDriver;
use Doctrine\Common\Persistence\Mapping\Driver\FileDriver as DoctrineFileDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Metadata\Driver\DriverChain;
use Metadata\Driver\DriverInterface;

/**
 * Adapt a Doctrine metadata driver
 *
 * @author Sander Marechal <sander@prezent.nl>
 * @author Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class DoctrineAdapter
{
    /**
     * Create a driver from a Doctrine registry
     *
     * @param ManagerRegistry $registry
     * @return DriverInterface
     */
    public static function fromRegistry(ManagerRegistry $registry)
    {
        $drivers = array();
        foreach ($registry->getManagers() as $manager) {
            $drivers[] = self::fromManager($manager);
        }

        return new DriverChain($drivers);
    }

    /**
     * Create a driver from a Doctrine object manager
     *
     * @param ObjectManager $om
     * @return DriverInterface
     */
    public static function fromManager(ObjectManager $om)
    {
        return self::fromMetadataDriver($om->getConfiguration()->getMetadataDriverImpl());
    }

    /**
     * Create a driver from a Doctrine metadata driver
     *
     * @param MappingDriver $omDriver
     * @return DriverInterface
     */
    public static function fromMetadataDriver(MappingDriver $omDriver)
    {
        if ($omDriver instanceof MappingDriverChain) {
            $drivers = array();
            foreach ($omDriver->getDrivers() as $nestedOmDriver) {
                $drivers[] = self::fromMetadataDriver($nestedOmDriver);
            }

            return new DriverChain($drivers);
        }

        if ($omDriver instanceof DoctrineAnnotationDriver) {
            return new AnnotationDriver($omDriver->getReader());
        }

        if ($omDriver instanceof DoctrineFileDriver) {
            $reflClass = new \ReflectionClass($omDriver);

            $driverName = $reflClass->getShortName();
            if ($omDriver instanceof SimplifiedYamlDriver || $omDriver instanceof SimplifiedXmlDriver) {
                $driverName = str_replace('Simplified', '', $driverName);
            }

            $class = 'Prezent\\Doctrine\\Translatable\\Mapping\\Driver\\' . $driverName;

            if (class_exists($class)) {
                return new $class($omDriver->getLocator());
            }
        }

        throw new \InvalidArgumentException('Cannot adapt Doctrine driver of class ' . get_class($omDriver));
    }
}
