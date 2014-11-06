<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Tests\Doctrine\Translatable\Mapping\Driver;

use Prezent\Doctrine\Translatable\Mapping\Driver\FileLocatorChain;

class FileLocatorChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function testFindMappingFileThrowsExceptionWithoutLocators()
    {
        $locator = new FileLocatorChain();
        $locator->findMappingFile('MyClassName');
    }

    public function testGetPaths()
    {
        $locator = $this->getMock('Doctrine\Common\Persistence\Mapping\Driver\FileLocator');
        $locator
            ->expects($this->once())
            ->method('getPaths')
            ->will($this->returnValue(array('mapping/doctrine')));

        $chain = new FileLocatorChain(array($locator));
        $this->assertEquals(array('mapping/doctrine'), $chain->getPaths());
    }

    public function testFindMappingFile()
    {
        $className = 'MyClassName';
        $locator = $this->getMock('Doctrine\Common\Persistence\Mapping\Driver\FileLocator');
        $locator
            ->expects($this->once())
            ->method('findMappingFile')
            ->with($className)
            ->will($this->returnValue($className . '.php'));

        $chain = new FileLocatorChain(array($locator));
        $this->assertEquals($className . '.php', $chain->findMappingFile($className));
    }
}
