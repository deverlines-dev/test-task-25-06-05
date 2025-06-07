<?php

namespace App\Services\Import;

use Exception;
use Generator;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException;
use OpenSpout\Reader\XLSX\Reader;

readonly class ImportXlsxService
{
    /**
     * @throws IOException
     * @throws Exception
     */
    public function import(string $path, int $chunkSize = 1000): Generator
    {
        $reader = new Reader();
        $chunk = [];

        try {
            $reader->open($path);

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $number => $row) {
                    $chunk[$number] = $row;

                    if (count($chunk) === $chunkSize) {
                        yield $chunk;

                        $chunk = [];
                    }
                }
            }

            if (!empty($chunk)) {
                yield $chunk;
            }

        } catch (ReaderNotOpenedException $e) {
            throw new Exception("Ошибка импорта $path - {$e->getMessage()}", 500, $e);
        } finally {
            $reader->close();
        }

    }

    /**
     * @param array<int, Cell> $cells
     * @param array<int, string> $tableHeaders
     *
     * @return array<int, string>
     */
    public function getTableItemWithColumnsName(array $cells, array $tableHeaders): array
    {
        $tableItem = [];

        foreach ($cells as $i => $cell) {
            $tableItem[$tableHeaders[$i]] = $this->getCellValue($cell);
        }

        return $tableItem;
    }

    /**
     * @param array<int, Cell> $cells
     *
     * @return array<int, string>
     */
    public function getTableHeaders(array $cells): array
    {
        return array_map(function (Cell $cell) {
            return $this->getCellValue($cell);
        }, $cells);
    }

    private function getCellValue(Cell $cell): mixed
    {
        $value = $cell->getValue();

        if (is_string($value)) {
            $value = trim($value);
        }

        return $value;
    }
}
