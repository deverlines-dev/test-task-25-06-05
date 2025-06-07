<?php

namespace App\Services\Import\ImportUsersXlsxTable;

use Exception;

readonly class ImportUsersXlsxTableValidatorService
{
    /**
     * @param array<int, string> $tableHeaders
     * @throws Exception
     */
    public function validateTableHeaders(array $tableHeaders, array $requiredHeaders): void
    {
        if ($tableHeaders === $requiredHeaders) {
            return;
        }

        throw new Exception(
            'Неверные заголовки таблицы, должны быть: ' . implode(', ', $requiredHeaders)
        );
    }

    /**
     * @throws Exception
     */
    public function validateFileExists(string $path): void
    {
        if (!file_exists($path)) {
            throw new Exception($path . ' - файл не найден');
        }
    }
}
