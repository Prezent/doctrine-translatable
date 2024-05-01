<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\Annotation;
use Doctrine\ORM\Mapping\MappingAttribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * Translatable annotation
 *
 * This annotation indicates the many-to-one relation to the translatable.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @NamedArgumentConstructor
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Translatable implements MappingAttribute
{
    /**
     * @var string
     */
    public $targetEntity;

    /**
     * @var string
     */
    public $referencedColumnName = 'id';


    public function __construct(
        ?string $targetEntity,
        ?string $referencedColumnName = 'id'
    ) {
        $this->targetEntity            = $targetEntity;
        $this->referencedColumnName    = $referencedColumnName;
    }
}
