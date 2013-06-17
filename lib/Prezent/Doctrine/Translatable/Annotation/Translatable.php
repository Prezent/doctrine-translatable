<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\Annotation;

/**
 * Translatable annotation
 *
 * This annotation indicates the many-to-one relation to the translatable.
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class Translatable
{
    /**
     * @var string
     * @Required
     */
    public $targetEntity;
}
