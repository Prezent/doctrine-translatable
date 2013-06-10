<?php

namespace Prezent\Tests\Tool;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Metadata\MetadataFactory;
use Prezent\Doctrine\Translatable\EventListener\TranslatableListener;
use Prezent\Doctrine\Translatable\Mapping\Driver\AnnotationDriver;

class ORMTestCase extends \PHPUnit_Framework_TestCase
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

        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . '/../Fixture'), true, null, null, false);
        return EntityManager::create($conn, $config, $this->getEventManager());
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
}
