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
 * Translations attribute
 *
 * This indicates the one-to-many relation to the translations.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Translations
{
    public string $targetEntity;

    public function __construct(string $targetEntity)
    {
        $this->targetEntity = $targetEntity;
    }
}
