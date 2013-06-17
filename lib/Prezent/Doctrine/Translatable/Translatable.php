<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable;

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
     * @param Translation $translation
     * @return self
     */
    public function addTranslation(Translation $translation);

    /**
     * Remove a translation
     *
     * @param Translation $translation
     * @return self
     */
    public function removeTranslation(Translation $translation);
}
