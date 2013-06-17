<?php

namespace Prezent\Tests\Doctrine\Translatable\Mapping;

use Prezent\Tests\Fixture\Basic;
use Prezent\Tests\Fixture\BasicTranslation;
use Prezent\Tests\Tool\ORMTestCase;

class TranslatableListenerTest extends ORMTestCase
{
    public function getFixtureClasses()
    {
        return array(
            'Prezent\\Tests\\Fixture\\Basic',
            'Prezent\\Tests\\Fixture\\BasicTranslation',
            'Prezent\\Tests\\Fixture\\Mapped',
            'Prezent\\Tests\\Fixture\\MappedTranslation',
            'Prezent\\Tests\\Fixture\\Inherited',
            'Prezent\\Tests\\Fixture\\InheritedTranslation',
        );
    }

    public function getEntities()
    {
        return array(
            array('Prezent\\Tests\\Fixture\\Basic', 'Prezent\\Tests\\Fixture\\BasicTranslation'),
            array('Prezent\\Tests\\Fixture\\Mapped', 'Prezent\\Tests\\Fixture\\MappedTranslation'),
            array('Prezent\\Tests\\Fixture\\Inherited', 'Prezent\\Tests\\Fixture\\InheritedTranslation'),
        );
    }

    /**
     * @dataProvider getEntities
     */
    public function testCurrentLocale($translatableClass, $translationClass)
    {
        // setup
        $em = $this->getEntityManager();
        $listener = $this->getTranslatableListener();
        $listener->setCurrentLocale('en');

        $en = new $translationClass();
        $en->setLocale('en')
           ->setName('foo');

        $entity = new $translatableClass();
        $entity->addTranslation($en);

        $em->persist($entity);
        $em->flush();
        $em->clear();
        // end setup

        $entity = $em->find($translatableClass, 1);

        $this->assertNotNull($entity);
        $this->assertInstanceOf($translationClass, $entity->getCurrentTranslation());
        $this->assertEquals('en', $entity->getCurrentTranslation()->getLocale());
    }

    /**
     * @dataProvider getEntities
     */
    public function testFallbackLocale($translatableClass, $translationClass)
    {
        // setup
        $em = $this->getEntityManager();
        $listener = $this->getTranslatableListener();
        $listener->setFallbackMode(true)
            ->setCurrentLocale('de')
            ->setFallbackLocale('en');

        $en = new $translationClass();
        $en->setLocale('en')
           ->setName('foo');

        $entity = new $translatableClass();
        $entity->addTranslation($en);

        $em->persist($entity);
        $em->flush();
        $em->clear();
        // end setup

        $entity = $em->find($translatableClass, 1);

        $this->assertNotNull($entity);
        $this->assertInstanceOf($translationClass, $entity->getCurrentTranslation());
        $this->assertEquals('en', $entity->getCurrentTranslation()->getLocale());
    }

    /**
     * @dataProvider getEntities
     */
    public function testFallbackOff($translatableClass, $translationClass)
    {
        // setup
        $em = $this->getEntityManager();
        $listener = $this->getTranslatableListener();
        $listener->setFallbackMode(false)
            ->setCurrentLocale('de');

        $en = new $translationClass();
        $en->setLocale('en')
           ->setName('foo');

        $entity = new $translatableClass();
        $entity->addTranslation($en);

        $em->persist($entity);
        $em->flush();
        $em->clear();
        // end setup

        $entity = $em->find($translatableClass, 1);

        $this->assertNotNull($entity);
        $this->assertNull($entity->getCurrentTranslation());
    }
}
