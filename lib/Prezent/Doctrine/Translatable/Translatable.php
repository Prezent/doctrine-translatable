<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable;

use Doctrine\Common\Collections\ArrayCollection;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * Interface for translatable entities
 */
interface Translatable
{
    /**
     * Get all translations
     *
     * @return ArrayCollection
     */
    public function getTranslations();

    /**
     * Add a new translation
     *
     * @param AbstractTranslation $translation
     * @return self
     */
    public function addTranslation(AbstractTranslation $translation);

    /**
     * Remove a translation
     *
     * @param AbstractTranslation $translation
     * @return self
     */
    public function removeTranslation(AbstractTranslation $translation);
}
