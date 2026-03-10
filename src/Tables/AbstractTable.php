<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Tables;

use Skywalker\LogViewer\Contracts\Table as TableContract;
use Skywalker\LogViewer\Contracts\Utilities\LogLevels as LogLevelsContract;

/**
 * Class     AbstractTable
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
abstract class AbstractTable implements TableContract
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var array<int, mixed> */
    private array $header = [];

    /** @var array<string|int, mixed> */
    private array $rows = [];

    /** @var array<string, mixed> */
    private array $footer = [];

    protected LogLevelsContract $levels;

    protected ?string $locale;

    /** @var array<string, mixed> */
    private array $data = [];

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Create a table instance.
     *
     * @param  array<string, mixed>  $data
     */
    public function __construct(array $data, LogLevelsContract $levels, ?string $locale = null)
    {
        $this->setLevels($levels);
        $localeConfig = config('log-viewer.locale');
        $this->setLocale(is_null($locale) ? (is_string($localeConfig) ? $localeConfig : null) : $locale);
        $this->setData($data);
        $this->init();
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Set LogLevels instance.
     *
     *
     * @return $this
     */
    protected function setLevels(LogLevelsContract $levels)
    {
        $this->levels = $levels;

        return $this;
    }

    /**
     * Set table locale.
     *
     * @return $this
     */
    protected function setLocale(?string $locale)
    {
        if (is_null($locale) || $locale === 'auto') {
            $locale = app()->getLocale();
        }

        $this->locale = $locale;

        return $this;
    }

    /**
     * Get table header.
     *
     * @return array<int, mixed>
     */
    public function header()
    {
        return $this->header;
    }

    /**
     * Get table rows.
     *
     * @return array<string|int, mixed>
     */
    public function rows()
    {
        return $this->rows;
    }

    /**
     * Get table footer.
     *
     * @return array<string, mixed>
     */
    public function footer()
    {
        return $this->footer;
    }

    /**
     * Get raw data.
     *
     * @return array<string, mixed>
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Set table data.
     *
     * @param  array<string, mixed>  $data
     * @return $this
     */
    private function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Prepare the table.
     */
    private function init(): void
    {
        $this->header = $this->prepareHeader($this->data);
        $this->rows = $this->prepareRows($this->data);
        $this->footer = $this->prepareFooter($this->data);
    }

    /**
     * Prepare table header.
     *
     * @param  array<string, mixed>  $data
     * @return array<int, mixed>
     */
    abstract protected function prepareHeader(array $data);

    /**
     * Prepare table rows.
     *
     * @param  array<string, mixed>  $data
     * @return array<string|int, mixed>
     */
    abstract protected function prepareRows(array $data);

    /**
     * Prepare table footer.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    abstract protected function prepareFooter(array $data);

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get log level color.
     *
     * @param  string  $level
     * @return string
     */
    protected function color($level)
    {
        return \log_styler()->color($level);
    }
}
