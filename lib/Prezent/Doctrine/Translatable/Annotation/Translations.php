<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\Annotation;

/**
 * Translations annotation
 *
 * This annotation indicates the one-to-many relation to the translations.
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class Translations
{
    /**
     * @var string
     * @Required
     */
    public $targetEntity;
}
