<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Contracts;

/**
 * Interface  Table
 *
 * @author    Mradul Sharma <skywalkerlknw@gmail.com>
 */
interface Table
{
    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get table header.
     *
     * @return array<int, mixed>
     */
    public function header();

    /**
     * Get table rows.
     *
     * @return array<int, mixed>
     */
    public function rows();

    /**
     * Get table footer.
     *
     * @return array<string, mixed>
     */
    public function footer();
}
