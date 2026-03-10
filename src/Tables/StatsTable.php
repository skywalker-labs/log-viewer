<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Tables;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Skywalker\LogViewer\Contracts\Utilities\LogLevels as LogLevelsContract;

/**
 * Class     StatsTable
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class StatsTable extends AbstractTable
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make a stats table instance.
     *
     * @param  array<string, mixed>  $data
     * @return static
     */
    public static function make(array $data, LogLevelsContract $levels, ?string $locale = null): self
    {
        return new self($data, $levels, $locale);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Prepare table header.
     *
     * @param  array<string, mixed>  $data
     * @return array<int, mixed>
     */
    protected function prepareHeader(array $data): array
    {
        /** @var array<int, mixed> $header */
        $header = array_merge(
            [
                'date' => __('Date'),
                'all' => __('All'),
            ],
            $this->levels->names($this->locale)
        );

        return $header;
    }

    /**
     * Prepare table rows.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function prepareRows(array $data): array
    {
        /** @var array<string, mixed> $rows */
        $rows = [];

        foreach ($data as $date => $levels) {
            if (is_string($date) && is_array($levels)) {
                $rows[$date] = array_merge(['date' => $date], $levels);
            }
        }

        return $rows;
    }

    /**
     * Prepare table footer.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function prepareFooter(array $data): array
    {
        /** @var array<string, int> $footer */
        $footer = [];

        foreach ($data as $levels) {
            if (! is_array($levels)) {
                continue;
            }
            foreach ($levels as $level => $count) {
                if (! is_string($level) || ! is_int($count)) {
                    continue;
                }
                if (! isset($footer[$level])) {
                    $footer[$level] = 0;
                }
                $footer[$level] += $count;
            }
        }

        return $footer;
    }

    /**
     * Get totals.
     *
     * @param  string|null  $locale
     * @return \Illuminate\Support\Collection<string, array<string, mixed>>
     */
    public function totals($locale = null)
    {
        $totals = Collection::make();

        foreach (Arr::except($this->footer(), 'all') as $level => $count) {
            $totals->put($level, [
                'label' => \log_levels()->get($level, $locale),
                'value' => $count,
                'color' => $this->color($level),
                'highlight' => $this->color($level),
            ]);
        }

        return $totals;
    }

    /**
     * Get json totals data.
     *
     * @param  string|null  $locale
     * @return string
     */
    public function totalsJson($locale = null)
    {
        return $this->totals($locale)->toJson(JSON_PRETTY_PRINT);
    }
}
