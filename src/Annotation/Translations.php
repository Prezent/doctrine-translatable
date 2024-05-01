<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\Annotation;

use Attribute;
use Doctrine\ORM\Mapping\MappingAttribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Utility\CollectionHelper;

/**
 * Translations annotation
 *
 * This annotation indicates the one-to-many relation to the translations.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @NamedArgumentConstructor
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Translations implements MappingAttribute
{
    /**
     * @var string
     */
    public $targetEntity;

    public function __construct(
        ?string $targetEntity = null
    ) {
        $this->targetEntity = $targetEntity;
    }
}
