<?php

namespace Prezent\Tests\Doctrine\Translatable\Mapping;

use Prezent\Tests\Fixture\Entry;
use Prezent\Tests\Fixture\EntryTranslation;
use Prezent\Tests\Tool\ORMTestCase;

class TranslatableListenerTest extends ORMTestCase
{
    public function getFixtureClasses()
    {
        return array(
            'Prezent\\Tests\\Fixture\\Entry',
            'Prezent\\Tests\\Fixture\\EntryTranslation',
        );
    }

    public function testCurrentLocale()
    {
        // setup
        $em = $this->getEntityManager();
        $listener = $this->getTranslatableListener();
        $listener->setCurrentLocale('en');

        $en = new EntryTranslation();
        $en->setLocale('en')
           ->setName('foo');

        $entry = new Entry();
        $entry->addTranslation($en);

        $em->persist($entry);
        $em->flush();
        $em->clear();
        // end setup

        $entry = $em->find('Prezent\\Tests\\Fixture\\Entry', 1);

        $this->assertNotNull($entry);
        $this->assertInstanceOf('Prezent\\Tests\\Fixture\\EntryTranslation', $entry->getCurrentTranslation());
        $this->assertEquals('en', $entry->getCurrentTranslation()->getLocale());
    }

    public function testFallbackLocale()
    {
        // setup
        $em = $this->getEntityManager();
        $listener = $this->getTranslatableListener();
        $listener->setCurrentLocale('de')
                 ->setFallbackLocale('en');

        $en = new EntryTranslation();
        $en->setLocale('en')
           ->setName('foo');

        $entry = new Entry();
        $entry->addTranslation($en);

        $em->persist($entry);
        $em->flush();
        $em->clear();
        // end setup

        $entry = $em->find('Prezent\\Tests\\Fixture\\Entry', 1);

        $this->assertNotNull($entry);
        $this->assertInstanceOf('Prezent\\Tests\\Fixture\\EntryTranslation', $entry->getCurrentTranslation());
        $this->assertEquals('en', $entry->getCurrentTranslation()->getLocale());
    }
}
