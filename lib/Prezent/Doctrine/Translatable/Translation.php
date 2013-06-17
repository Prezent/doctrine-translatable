<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prezent\Doctrine\Translatable;

/**
 * Interface for translation entities
 */
interface Translation
{
    /**
     * Get the translatable object
     *
     * @return Translatable
     */
    public function getTranslatable();

    /**
     * Set the translatable object
     *
     * @param Translatable $translatable
     * @return self
     */
    public function setTranslatable(Translatable $translatable);

    /**
     * Get the locale
     *
     * @return string
     */
    public function getLocale();

    /**
     * Set the locale
     *
     * @param string $locale
     * @return self
     */
    public function setLocale($locale);
}
