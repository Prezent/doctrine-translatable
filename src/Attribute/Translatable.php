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
 * Translatable attribute
 *
 * This indicates the many-to-one relation to the translatable.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Translatable
{
    public string $targetEntity;

    public string $referencedColumnName = 'id';

    public function __construct(string $targetEntity, string $referencedColumnName = 'id')
    {
        $this->targetEntity = $targetEntity;
        $this->referencedColumnName = $referencedColumnName;
    }
}
