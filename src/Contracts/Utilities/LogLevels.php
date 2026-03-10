<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Contracts\Utilities;

use Illuminate\Translation\Translator;

/**
 * Interface  LogLevels
 *
 * @author    Mradul Sharma <skywalkerlknw@gmail.com>
 */
interface LogLevels
{
    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Set the Translator instance.
     *
     *
     * @return self
     */
    public function setTranslator(Translator $translator);

    /**
     * Get the selected locale.
     *
     * @return string
     */
    public function getLocale();

    /**
     * Set the selected locale.
     *
     * @return self
     */
    public function setLocale(?string $locale);

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the log levels.
     *
     * @param  bool  $flip
     * @return array<string, string>
     */
    public function lists($flip = false);

    /**
     * Get translated levels.
     *
     * @param  string|null  $locale
     * @return array<string, string>
     */
    public function names($locale = null);

    /**
     * Get PSR log levels.
     *
     * @return array<string, string>
     */
    public static function all(bool $flip = false);

    /**
     * Get the translated level.
     *
     * @param  string  $key
     * @param  string|null  $locale
     * @return string
     */
    public function get($key, $locale = null);
}
