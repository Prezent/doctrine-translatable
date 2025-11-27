<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Tests\Doctrine\Translatable\Mapping\Driver;

use Prezent\Doctrine\Translatable\Mapping\Driver\AttributeDriver;

class AttributeDriverTest extends BaseDriverTestCase
{
    protected function getDriver()
    {
        // Use attribute-based mapping driver instead of the removed annotation-based one
        return new AttributeDriver();
    }
}
