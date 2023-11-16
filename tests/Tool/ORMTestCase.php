<?php

namespace Prezent\Tests\Tool;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\PsrCachedReader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver as ORMAnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Metadata\MetadataFactory;
use PHPUnit\Framework\TestCase;
use Prezent\Doctrine\Translatable\EventListener\TranslatableListener;
use Prezent\Doctrine\Translatable\Mapping\Driver\AnnotationDriver;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

abstract class ORMTestCase extends TestCase
{
    private $em;

    private $evm;

    private $listener;

    public function getEntityManager()
    {
        if (!$this->em) {
            $this->em = $this->createEntityManager();
        }

        return $this->em;
    }

    public function createEntityManager()
    {
        $conn = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        AnnotationRegistry::registerFile(
            realpath(__DIR__ . '/../../vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/AnnotationDriver.php')
        );

        $reader = new AnnotationReader();
        $reader = new PsrCachedReader($reader, new ArrayAdapter());

        $config = Setup::createConfiguration(true);
        $config->setMetadataDriverImpl(new ORMAnnotationDriver($reader, array(__DIR__ . '/../Fixture')));

        $em = EntityManager::create($conn, $config, $this->getEventManager());

        $schemaTool = new SchemaTool($em);
        $schemaTool->createSchema(array_map(function ($class) use ($em) {
            return $em->getClassMetadata($class);
        }, $this->getFixtureClasses()));

        return $em;
    }

    public function getEventManager()
    {
        if (!$this->evm) {
            $this->evm = $this->createEventManager();
        }

        return $this->evm;
    }

    public function createEventManager()
    {
        $evm = new EventManager();
        $evm->addEventSubscriber($this->getTranslatableListener());

        return $evm;
    }

    public function getTranslatableListener()
    {
        if (!$this->listener) {
            $this->listener = $this->createTranslatableListener();
        }

        return $this->listener;
    }

    public function createTranslatableListener()
    {
        return new TranslatableListener(new MetadataFactory(new AnnotationDriver(new AnnotationReader())));
    }

    public function getFixtureClasses()
    {
        return array();
    }
}
