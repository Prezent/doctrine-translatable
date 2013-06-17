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
interface TranslationInterface
{
    /**
     * Get the translatable object
     *
     * @return TranslatableInterface
     */
    public function getTranslatable();

    /**
     * Set the translatable object
     *
     * @param TranslatableInterface $translatable
     * @return self
     */
    public function setTranslatable(TranslatableInterface $translatable = null);

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
