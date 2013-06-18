<?php

namespace Prezent\Tests\Doctrine\Translatable\Mapping;

use Prezent\Tests\Tool\ORMTestCase;

class TranslatableListenerTest extends ORMTestCase
{
    public function getFixtureClasses()
    {
        $fixtures = array(
            'Prezent\\Tests\\Fixture\\Basic',
            'Prezent\\Tests\\Fixture\\BasicTranslation',
            'Prezent\\Tests\\Fixture\\Mapped',
            'Prezent\\Tests\\Fixture\\MappedTranslation',
            'Prezent\\Tests\\Fixture\\Inherited',
            'Prezent\\Tests\\Fixture\\InheritedTranslation',
        );

        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            $fixtures[] = 'Prezent\\Tests\\Fixture\\Mixin';
            $fixtures[] = 'Prezent\\Tests\\Fixture\\MixinTranslation';
        }

        return $fixtures;
    }

    public function getEntities()
    {
        return array(
            array('5.3.0', 'Prezent\\Tests\\Fixture\\Basic',     'Prezent\\Tests\\Fixture\\BasicTranslation'),
            array('5.3.0', 'Prezent\\Tests\\Fixture\\Mapped',    'Prezent\\Tests\\Fixture\\MappedTranslation'),
            array('5.3.0', 'Prezent\\Tests\\Fixture\\Inherited', 'Prezent\\Tests\\Fixture\\InheritedTranslation'),
            array('5.4.0', 'Prezent\\Tests\\Fixture\\Mixin',     'Prezent\\Tests\\Fixture\\MixinTranslation'),
        );
    }

    /**
     * @dataProvider getEntities
     */
    public function testCurrentLocale($version, $translatableClass, $translationClass)
    {
        if (version_compare(PHP_VERSION, $version) < 0) {
            $this->markTestSkipped('Traits require PHP 5.4');
        }

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
    public function testFallbackLocale($version, $translatableClass, $translationClass)
    {
        if (version_compare(PHP_VERSION, $version) < 0) {
            $this->markTestSkipped('Traits require PHP 5.4');
        }

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
    public function testFallbackOff($version, $translatableClass, $translationClass)
    {
        if (version_compare(PHP_VERSION, $version) < 0) {
            $this->markTestSkipped('Traits require PHP 5.4');
        }

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
