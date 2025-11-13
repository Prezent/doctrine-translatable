<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable\Attribute;

use Attribute;

/**
 * This indicates the property where the current translation object
 * must be loaded.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class FallbackLocale
{
}
