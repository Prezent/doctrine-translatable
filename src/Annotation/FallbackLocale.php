<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\Annotation;
use Doctrine\ORM\Mapping\MappingAttribute;

/**
 * CurrentTranslation annotation
 *
 * This annotation indicates the property where the current translation object
 * must be loaded.
 *
 * @Annotation
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class FallbackLocale implements MappingAttribute
{
}
