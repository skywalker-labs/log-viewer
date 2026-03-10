<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Utilities;

use Illuminate\Support\Arr;
use Illuminate\Translation\Translator;
use Psr\Log\LogLevel;
use ReflectionClass;
use Skywalker\LogViewer\Contracts\Utilities\LogLevels as LogLevelsContract;

/**
 * Class     LogLevels
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class LogLevels implements LogLevelsContract
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The log levels.
     *
     * @var array<string, string>
     */
    protected static $levels = [];

    /**
     * The Translator instance.
     */
    private Translator $translator;

    /**
     * The selected locale.
     */
    private string $locale = 'auto';

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * LogLevels constructor.
     *
     * @param  string  $locale
     */
    public function __construct(Translator $translator, $locale)
    {
        $this->setTranslator($translator);
        $this->setLocale($locale);
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Set the Translator instance.
     *
     *
     * @return $this
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * Get the selected locale.
     */
    public function getLocale(): string
    {
        return $this->locale === 'auto'
            ? $this->translator->getLocale()
            : $this->locale;
    }

    /**
     * Set the selected locale.
     *
     * @return $this
     */
    public function setLocale(?string $locale): self
    {
        $this->locale = (is_null($locale) || $locale === '') ? 'auto' : $locale;

        return $this;
    }

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
    public function lists($flip = false)
    {
        return static::all($flip);
    }

    /**
     * Get translated levels.
     *
     * @param  string|null  $locale
     * @return array<string, string>
     */
    public function names($locale = null)
    {
        $levels = static::all(true);

        array_walk($levels, function (&$name, $level) use ($locale) {
            $name = $this->get($level, $locale);
        });

        return $levels;
    }

    /**
     * Get PSR log levels.
     *
     * @return array<string, string>
     */
    public static function all(bool $flip = false): array
    {
        if (empty(static::$levels)) {
            /** @var array<string, string> $constants */
            $constants = (new ReflectionClass(LogLevel::class))->getConstants();
            static::$levels = $constants;
        }

        return $flip ? array_flip(static::$levels) : static::$levels;
    }

    /**
     * Get the translated level.
     *
     * @param  string  $key
     * @param  string|null  $locale
     * @return string
     */
    public function get($key, $locale = null): string
    {
        if (! is_string($key)) {
            return '';
        }

        if ($this->translator->has("log-viewer::levels.{$key}", $locale)) {
            $translated = $this->translator->get("log-viewer::levels.{$key}", [], $locale);

            return is_string($translated) ? $translated : $key;
        }

        /** @var array<string, string> $translations */
        $translations = [
            'all' => 'All',
            LogLevel::EMERGENCY => 'Emergency',
            LogLevel::ALERT => 'Alert',
            LogLevel::CRITICAL => 'Critical',
            LogLevel::ERROR => 'Error',
            LogLevel::WARNING => 'Warning',
            LogLevel::NOTICE => 'Notice',
            LogLevel::INFO => 'Info',
            LogLevel::DEBUG => 'Debug',
        ];

        $result = Arr::get($translations, $key, $key);

        return is_string($result) ? $result : '';
    }
}
