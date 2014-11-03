<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Tests\Doctrine\Translatable\Mapping\Driver;

use Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator;
use Prezent\Doctrine\Translatable\Mapping\Driver\XmlDriver;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class XmlDriverTest extends BaseDriverTest
{
    protected function getDriver()
{
    $locator = new SymfonyFileLocator(array(
        __DIR__ . '/../../../../Fixture/xml' => 'Prezent\\Tests\\Fixture',
    ), '.xml');

    return new XmlDriver($locator);
}
}