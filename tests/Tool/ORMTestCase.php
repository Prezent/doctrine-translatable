<?php

namespace Prezent\Tests\Tool;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use Metadata\MetadataFactory;
use PHPUnit\Framework\TestCase;
use Prezent\Doctrine\Translatable\EventListener\TranslatableListener;
use Prezent\Doctrine\Translatable\Mapping\Driver\AttributeDriver as TranslatableAttributeDriver;

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

        // Use Doctrine ORM 3 attribute mapping for the test entities in tests/Fixture
        $config = ORMSetup::createAttributeMetadataConfiguration(array(__DIR__ . '/../Fixture'), true);

        // Doctrine ORM 3/4: always construct EntityManager with a DBAL Connection instance
        $connection = DriverManager::getConnection($conn);
        $em = new EntityManager($connection, $config, $this->getEventManager());

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
        // Use attribute-based translatable metadata for the test entities
        return new TranslatableListener(new MetadataFactory(new TranslatableAttributeDriver()));
    }

    public function getFixtureClasses()
    {
        return array();
    }
}
