<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Tests\Doctrine\Translatable\Mapping\Driver;

use Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator;
use Prezent\Doctrine\Translatable\Mapping\Driver\YamlDriver;

class YamlDriverTest extends BaseDriverTest
{
    protected function getDriver()
    {
        $locator = new SymfonyFileLocator(array(
            __DIR__ . '/../../../../Fixture/yaml' => 'Prezent\\Tests\\Fixture',
        ), '.yml');

        return new YamlDriver($locator);
    }
}
