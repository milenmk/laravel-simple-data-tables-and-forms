<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Contracts;

use Symfony\Component\HttpFoundation\StreamedResponse;

interface WithExportInterface
{
    /**
     * Export table data to the specified format.
     */
    public function export(string $format = 'csv'): ?StreamedResponse;
}
